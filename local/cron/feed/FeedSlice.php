<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

class FeedSlice
{
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

	protected function getProduct(){
		global $DB;
		$res = $DB->Query('SELECT * FROM FeedSlice');
		$products = [];
		while ($product = $res->Fetch()) {
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

	protected function getProductsForUpd($products){
		$products_update = [];
		foreach ($products as $key => $value) {

			if($value['optimal_price'] == null){
				$products_update[$key] = $value;
			}
			/*
			if(count($products_update) == 5000){
				break;
			}
			*/
		}
		return $products_update;
	}

	protected function getPrice($products, $site_id){
		$products_update = $products;
		foreach ($products as $productId => $arPrice) {
			#$optimal_price = CCatalogProduct::GetOptimalPrice($productId, 1, [], 'N', [$arPrice], $site_id)['RESULT_PRICE']['DISCOUNT_PRICE'];
			$optimal_price = CCatalogProduct::GetOptimalPrice($productId, 1, [], 'N', [], $site_id)['RESULT_PRICE']['DISCOUNT_PRICE'];
			$products_update[$productId]['optimal_price'] = $optimal_price;
		}

		return $products_update;
	}

	protected function setSliceContinue(){
		global $DB;
		$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'feed slice', TIME = 0 WHERE id = 0;");
	}

	protected function setContinue(){
		global $DB;
		$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'no work', TIME = 0 WHERE id = 0;");
	}

	protected function clearBD() {
		global $DB;
		$DB->query('DELETE FROM FeedSlice');
	}

	public function run($site_id,$product_ar = null)
	{
		$products = $this->getProduct();
		if(count($products) > 0){
			//if all products update return this products
			$products_update = $this->getProductsForUpd($products);
			if(count($products_update) == 0){
				$this->clearBD();
				return $products;
			}
			else
			{
				$products_update = $this->getPrice($products_update, $site_id);
				$this->addProductsDB($products_update);
				//if this last products for update
				/*
				if (count($products_update) == 5000)
				{
					$products = $this->getProduct();
					$products_update = $this->getProductsForUpd($products);
					if(count($products_update) > 0){
						$this->setSliceContinue();
					}
					else
					{
						$this->setContinue();
					}
				}
				else
				{
					$this->setContinue();
				}
				*/
				$this->setContinue();
				return false;
			}
		}
		else
		{
			if(count($product_ar))
			{
				/*
				if (count($product_ar) < 5000){
					return $this->getPrice($product_ar, $site_id);
				}
				*/
				return $this->getPrice($product_ar, $site_id);
				$this->addProductsDB($product_ar);
				$this->run($site_id);
			}
			else{
				return array();
			}
		}
	}
}