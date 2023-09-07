<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class FeedSlice
{
	const SLICE_SIZE = 5000;
	protected $siteId;
	protected $productArr;
	protected $flagReturnNow = null;
	protected $productsForUpdate;

	/*
	*$siteId - string
	*$params array $productArr = array(
	*	productId =>array(
    *        'ID' => PRICE_ID,
    *        'PRICE'
    *        'CURRENCY'
    *        'CATALOG_GROUP_ID'
    *        'optimal_price' => null
    *    ),
	*)
	*/
	public function __construct($siteId, $productArr){
		$this->siteId = $siteId;
		$products = $this->getProduct();
		if(count($products)){
			$this->productArr = $products;
		}
		else{
			$this->addProductsDB($productArr);
			$this->productArr = $productArr;
		}
		$this->productsForUpdate = $this->getProductsForUpd();
	}

	/*
	*if productArr < 5000 or have strings from FeedSlice and any this string have optimal price - true
	*else false
	*/
	public function checkReturnNow(){
		if (
			count($this->productArr) =< self::SLICE_SIZE
			|| 
			(
				count($this->productsForUpdate) == 0 
				&& 
				count($this->productArr) > 0
			)
		) {	
			$this->flagReturnNow = true;
		} else {
			$this->flagReturnNow = false;
		}
		return $this->flagReturnNow;
	}

	public function run() {
		if (is_null($this->flagReturnNow)) {	
			$this->checkReturnNow();
		}

		if ($this->flagReturnNow){
			if (count($this->productsForUpdate)) {
				return $this->getPrice($this->productArr, $this->siteId);				
			} else {
				$this->clearBD();
				return $this->productArr;
			}
		}
		else {
			$productsWithPrices = $this->getPrice($this->productsForUpdate, $this->siteId);
			$this->addProductsDB($productsWithPrices);
			return false;
		}
  //       if(count($products) > 0){
  //           //if all products update return this products
  //           $productsForUpdate = $this->getProductsForUpd($products);
  //           if(count($productsForUpdate) == 0){
  //               $this->clearBD();
  //               return $products;
  //           }
  //           else{
  //               $productsForUpdate = $this->getPrice($productsForUpdate, $this->siteId);
  //               $this->addProductsDB($productsForUpdate);
  //               //if this last products for update
  //               if(count($productsForUpdate) == self::SLICE_SIZE){
  //                   $products = $this->getProduct();
  //                   $productsForUpdate = $this->getProductsForUpd($products);
  //                   if(count($productsForUpdate) > 0){
  //                       $this->setSliceContinue();
  //                   }
  //                   else{
  //                       $this->setContinue();
  //                   }
  //               }
  //               else{
  //                   $this->setContinue();
  //               }
  //               return false;
  //           }
  //       }
  //       else{
  //           if(count($this->productArr)){
  //               if(count($this->productArr) < self::SLICE_SIZE){
  //                   return $this->getPrice($this->productArr, $this->siteId);
  //               }
  //               $this->addProductsDB($this->productArr);
  //               $this->run($this->siteId);
  //           }
  //           else{
  //               return array();
		// 	}
		// }
	}

	protected function getProduct(){
		global $DB;
		$ob = $DB->Query('SELECT * FROM FeedSlice');
		$products = [];
		while ($product = $ob->Fetch()) {
			$products[$product['product_id']] = 
			[
			 	'ID' => $product['price_id'],
			 	'PRICE' => $product['price'],
			 	'CURRENCY' => $product['currency'],
			 	'CATALOG_GROUP_ID' => $product['catalog_group_id'],
			 	'optimal_price' => $product['optimal_price']
			];
		}
		return $products;
	}

	/*
	* $products = [ product_id => price]
	*/
	protected function addProductsDB($products){
		global $DB;
		$query = 'insert into FeedSlice values ';
		foreach ($products as $key => $value) {
			$query .= ' (' . 
							$key . ',' . 
							$value['ID'] . ',' . 
							$value['PRICE'] . ',' . 
							'"' . $value['CURRENCY'] . '",' . 
							$value['CATALOG_GROUP_ID'] . ',' . 
							($value['optimal_price'] === NULL ? 'null' : $value['optimal_price']) . 
						'),';
		}
		$query = substr($query, 0, -1) . ' ON DUPLICATE KEY UPDATE 
			price_id=VALUES(price_id), 
			price=VALUES(price),
			currency=VALUES(currency),
			catalog_group_id=VALUES(catalog_group_id),
			optimal_price=VALUES(optimal_price)';
		$DB->Query($query);
	}

	protected function getProductsForUpd(){
		global $DB;

		$ob = $DB->Query("SELECT * FROM FeedSlice WHERE optimal_price = NULL");
		$products = [];
		while ($product = $ob->Fetch()) {
			$products[$product['product_id']] = 
			[
			 	'ID' => $product['price_id'],
			 	'PRICE' => $product['price'],
			 	'CURRENCY' => $product['currency'],
			 	'CATALOG_GROUP_ID' => $product['catalog_group_id'],
			 	'optimal_price' => $product['optimal_price']
			];
		}
		return $products;
		// foreach ($products as $key => $value) {

		// 	if(!array_key_exists('optimal_price', $value) || $value['optimal_price'] == null){
		// 		$value['optimal_price'] = null;
		// 		$productsForUpdate[$key] = $value;
		// 	}
		// 	if(count($productsForUpdate) == self::SLICE_SIZE){
		// 		break;
		// 	}
		// }
		// return $productsForUpdate;
	}

	protected function getPrice($products, $siteId){
		$productsForUpdate = $products;
		foreach ($products as $productId => $arPrice) {
			$optimal_price = CCatalogProduct::GetOptimalPrice($productId, 1, [], 'N', [$arPrice], $siteId)['RESULT_PRICE']['DISCOUNT_PRICE'];
			$productsForUpdate[$productId]['optimal_price'] = $optimal_price;
		}

		return $productsForUpdate;
	}

	// protected function setSliceContinue(){
	// 	global $DB;
	// 	$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'feed slice', TIME = 0 WHERE id = 0;");
	// }

	// protected function setContinue(){
	// 	global $DB;
	// 	$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'no work', TIME = 0 WHERE id = 0;");
	// }

	protected function clearBD() {
		global $DB;
		$DB->query('DELETE FROM FeedSlice');
	}

	private function create_table(){
		global $DB;
		$DB->Query('
			CREATE TABLE FeedSlice(
				product_id int primary key not null,
				price_id int,
				price FLOAT(10,2),
                currency char(10),
                catalog_group_id int,
                optimal_price FLOAT(10,2)
			);
		');
	}
}