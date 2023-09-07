<?
ini_set('display_errors',1);
error_reporting(E_ALL);

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__,3).'/..');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/YandexFeed.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/FeedSlice.php");

class FeedWorker
{
	function __construct() {}

	protected function runStart()
	{
		global $DB;

		$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'work', TIME = ".time()." WHERE id = 0;");
	}

	protected function runEnd()
	{
		global $DB;

		$DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'no work' WHERE id = 0;");
	}

	protected function checkRun()
	{
		global $DB;

		$real_time = time();
		$params = $DB->Query("SELECT TIME,SCRIPT_RUN FROM FeedRun WHERE id = 0")->Fetch();
		if(($real_time - $params['TIME']) < 1800 || $params['SCRIPT_RUN'] == 'work'){
			return false;
		}
		else {
			if($params['SCRIPT_RUN'] == 'feed slice'){
				return $params['SCRIPT_RUN'];
			}
			else{
				return true;
			}
		}
	}

	protected function checkEasyRun(){
		global $DB;

		$params = $DB->Query("SELECT TIME,SCRIPT_RUN FROM FeedRun WHERE id = 0")->Fetch();
		if($params['SCRIPT_RUN'] == 'work'){
			return false;
		}
		else {
			if($params['SCRIPT_RUN'] == 'feed slice'){
				return $params['SCRIPT_RUN'];
			}
			return true;
		}
	}

	protected function getFeedStack(){
		global $DB;

		$feedStackObj = $DB->Query("SELECT id, site_id, catalog_group_id FROM FeedStack");
		$feedStack = [];
		while ($feed = $feedStackObj->Fetch()) {
			$feedStack[] = $feed;
		}
		return $feedStack;
	}

	protected function clearFeedStack($id){
		global $DB;

		return $feedStackObj = $DB->Query("DELETE FROM FeedStack WHERE id = ".$id);
	}


	public function run()
	{
		// $this->checkRun()
		// $this->checkEasyRun()

		$this->runStart();
		ini_set('memory_limit', '10240M');

		$feedStack = [
			['site_id' => 's0', 'catalog_group_id' => 1], // tiptop-shop.ru/feed/yandex.xml
			['site_id' => 's0', 'catalog_group_id' => 4], // tiptop-shop.ru/feed/yandex_ekb.xml
			['site_id' => 's0', 'catalog_group_id' => 2], // tiptop-shop.ru/feed/yandex_spb.xml
			['site_id' => 's1', 'catalog_group_id' => 1], // drvt.shop/feed/yandex.xml
			['site_id' => 's1', 'catalog_group_id' => 4], // drvt.shop/feed/yandex_ekb.xml
			['site_id' => 's1', 'catalog_group_id' => 2], // drvt.shop/feed/yandex_spb.xml
			['site_id' => 's2', 'catalog_group_id' => 1], // shop-roca.ru/feed/yandex.xml
			['site_id' => 's2', 'catalog_group_id' => 4], // shop-roca.ru/feed/yandex_ekb.xml
			['site_id' => 's2', 'catalog_group_id' => 2], // shop-roca.ru/feed/yandex_spb.xml
			['site_id' => 's3', 'catalog_group_id' => 1], // hg-online.ru/feed/yandex.xml
			['site_id' => 's3', 'catalog_group_id' => 4], // hg-online.ru/feed/yandex_ekb.xml
			['site_id' => 's3', 'catalog_group_id' => 2], // hg-online.ru/feed/yandex_spb.xml
			['site_id' => 's5', 'catalog_group_id' => 1], // shop-jd.ru/feed/yandex.xml
			['site_id' => 's5', 'catalog_group_id' => 4], // shop-jd.ru/feed/yandex_ekb.xml
			['site_id' => 's5', 'catalog_group_id' => 2], // shop-jd.ru/feed/yandex_spb.xml
			['site_id' => 's6', 'catalog_group_id' => 1], // shop-gr.ru/feed/yandex.xml
			['site_id' => 's6', 'catalog_group_id' => 4], // shop-gr.ru/feed/yandex_ekb.xml
			['site_id' => 's6', 'catalog_group_id' => 2], // shop-gr.ru/feed/yandex_spb.xml
			['site_id' => 's8', 'catalog_group_id' => 1], // geberit-shop.ru/feed/yandex.xml
			['site_id' => 's8', 'catalog_group_id' => 4], // geberit-shop.ru/feed/yandex_ekb.xml
			['site_id' => 's8', 'catalog_group_id' => 2], // geberit-shop.ru/feed/yandex_spb.xml
			['site_id' => 'l1', 'catalog_group_id' => 1], // swet-online.ru/feed/yandex.xml
			['site_id' => 'l1', 'catalog_group_id' => 4], // swet-online.ru/feed/yandex_ekb.xml
			['site_id' => 'l1', 'catalog_group_id' => 2], // swet-online.ru/feed/yandex_spb.xml
		];
		foreach ($feedStack as $feed)
		{
			$yandexFeed = new YandexFeed( $feed['site_id'], $feed['catalog_group_id'] );
			$res = $yandexFeed->Generate();
		}

		$this->runEnd();

		/*
		$checkRun = $this->checkEasyRun();
		if( $checkRun ) {
			$this->runStart();
			ini_set('memory_limit', '2048M');
			$feedStack = $this->getFeedStack();
			if ( count($feedStack) > 0 ) {
				if($checkRun === 'feed slice'){
					$feedSlice = new FeedSlice;
					foreach ($feedStack as $feed) {
						$feedSlice->run($feed['site_id']);
						return false;
					}
				}
				else{
					$productCounter = 0;
					foreach ($feedStack as $feed) {
						$yandexFeed = new YandexFeed( $feed['site_id'], $feed['catalog_group_id'] );
						$res = $yandexFeed->Generate();
						$productCounter += $res;
						if($res === false){
							return false;
						}
						if($productCounter > 4000 && $res < 5000){
							#break;
						}
						$this->clearFeedStack($feed['id']);
					}
				}
			}
			$this->runEnd();
		}
		*/
	}
}

$worker = new FeedWorker;
$worker->run();