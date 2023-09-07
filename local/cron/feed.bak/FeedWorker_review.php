<?
$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__,3).'/..');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/YandexFeed.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/cron/feed/FeedSlice.php");

class FeedWorker
{
	public function run() 
	{      
		// $this->checkRun()
		// $this->checkRunEasy() //no check run on last 30 minutes

		$checkRun = $this->checkRunEasy();
		if($checkRun == 'work') {

			$this->runStart();

			$feedStack = $this->getFeedStack();
			//if have work
			if (count($feedStack) > 0 ) {

				ini_set('memory_limit', '2048M');

				$productCounter = 0;
                foreach ($feedStack as $feed) {
                    $yandexFeed = new YandexFeed($feed['site_id'], $feed['catalog_group_id']);
                    $productCounter += $yandexFeed->Generate();

                    //if feed have >5000 products no end this feed
                    if($this->checkRunEasy() == 'feed slice'){
                    	return false;
                    }

                    //if update this feed remove him from db
                    $this->clearFeedStack($feed['id']);

                    //if summ feeds product > 4000 stop this hit
                    if($productCounter > 4000){
                    	break;
                    }
                }					
			}			
			$this->runEnd();
		}
	}

	protected function runStart()
	{
		global $DB;

		 $DB->Query("UPDATE FeedRun SET SCRIPT_RUN = 'work', TIME = " . time() . " WHERE id = 0;");
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
	    $params = $DB->Query("SELECT TIME, SCRIPT_RUN FROM FeedRun WHERE id = 0")->Fetch();

	    //if script work or run on last 30 minutes
	    if(($real_time - $params['TIME']) < 1800 || $params['SCRIPT_RUN'] == 'work'){
	        return 'stop work';
	    }
	    else {
	    	if($params['SCRIPT_RUN'] == 'feed slice'){
	    		return $params['SCRIPT_RUN'];
	    	}
	    	else{
	        	return 'work';
	    	}
	    }
	}

	protected function checkRunEasy(){
	    global $DB;

	    $params = $DB->Query("SELECT TIME,SCRIPT_RUN FROM FeedRun WHERE id = 0")->Fetch();

	 	//if script work
	    if($params['SCRIPT_RUN'] == 'work'){
	        return 'stop work';
	    }
	    else {
	    	if($params['SCRIPT_RUN'] == 'feed slice'){
	    		return $params['SCRIPT_RUN'];
	    	}
	    	else{
	        	return 'work';
	    	}	    		
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
}

$worker = new FeedWorker;
$worker->run();