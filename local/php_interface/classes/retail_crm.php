<?

function retailCrmBeforeOrderSend($order, $arFields)
{
	$order['customFields']['promo'] = false;
	$order['customFields']['promo_code'] = "";
	if(intval($arFields["ID"])>0)
	{
	    $coupon=false;
	    $couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(
	        array(
	            'select' => array('COUPON'),
	            'filter' => array('=ORDER_ID' => $arFields["ID"])
	        )
	    );
	    while ($data = $couponList->fetch())
	    {
	        $coupon=$data["COUPON"];
	    }

	    if($coupon)
	    {
	        $order['customFields']['promo'] = true;
	        $order['customFields']['promo_code'] = $coupon;
	    }
	}
	$source=[];
	$customSources=[];
	//$source=["source" => "market","medium" => "cpc","campaign" => "adgasm_market_search_rus_smesiteli","keyword" => "416159","content" => "33565001"];
	foreach (initUTMS::UTMS as $retailCode => $code) {
		if(isset($_COOKIE[$code]) && strlen($_COOKIE[$code])>0){
			$source[$retailCode]=$_COOKIE[$code];
			foreach (initUTMS::rUTMS[$code] as $sourceItem) {
				$order['customFields'][$sourceItem]=$source[$retailCode];
			}
		}
	}
	if(count($source)>0)
	{

		$order['source']=$source;
	}
	return $order;
}

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('main', 'OnBeforeProlog', array('initUTMS', 'OnBeforeProlog'));

class initUTMS
{
	const UTMS = [
		'source'=>'utm_source',
		'medium'=>'utm_medium',
		'campaign'=>'utm_campaign',
		'keyword'=>'utm_term',
		'content'=>'utm_content',
	];
	const rUTMS = [
		'utm_source'=>['cltchpd4fwh6y','cltchottbaw9f'],
		'utm_medium'=>['cltchrm4rvr24','cltch4ujcdv4b'],
		'utm_campaign'=>['cltchrfs7mu14','cltchvmnwsnc3'],
		'utm_term'=>['cltchrenpqyfq'],
		'utm_content'=>['cltch8bhe0m04','cltcharba8l6w'],
	];

	function OnBeforeProlog()
	{
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$request = $context->getRequest();
		$isAdminSection = $request->isAdminSection();
		if (!$isAdminSection)
		{
			foreach (self::UTMS as $key)
			{
				if(isset($_GET[$key]) && strlen($_GET[$key])>0){
					$cookie = new \Bitrix\Main\Web\Cookie($key, $_GET[$key]);
					$cookie->setDomain(explode(":", \Bitrix\Main\Application::getInstance()->getContext()->getServer()->getHttpHost())[0]);
					\Bitrix\Main\Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
					setcookie($key, $cookie->getValue(), 
						time()+60 * 60 * 24 * 30, '/',$cookie->getDomain()); 
					//если по какой-то причине не сработало сохранение в куки в формате D7
				}
			}
		}
	}	

}
?>