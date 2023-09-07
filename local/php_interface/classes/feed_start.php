<?
//class Singlton
class FeedRun
{
    private static $instances = [];
    protected static $connection;
    protected static $old_prices = [];
    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    protected static function create_FeedStack_table(){
        global $DB;
        $DB->Query(
            'Create table FeedStack(
                id int auto_increment primary key not null,
                site_id char(5) NOT NULL,
                catalog_group_id int not null
            )'
        );
        $DB->Query(
            'create table FeedRun(
                id int Primary key not null,
                SCRIPT_RUN varchar(128) not null,
                TIME int not null
            );'
        );

    }

    public static function getInstance()
    {
        static::$connection = \Bitrix\Main\Application::getConnection();
        $cls = static::class;
        if (!isset(static::$instances[$cls])) {
            static::$instances[$cls] = new static;
        }

        return static::$instances[$cls];
    }

    public static function DiscountChange($id,$arFields = null){
        if(!$arFields){
            $arFields = CCatalogDiscount::GetByID($id);
        }
        $catalogGroups = CCatalogGroup::GetGroupsList(['BUY'=>'Y']);
        while ($catalogGroup = $catalogGroups->Fetch()) {
            static::run($arFields['SITE_ID'], $catalogGroup['CATALOG_GROUP_ID']);
        }
    }

    public static function iblockUpdatePre($id,&$arParams){
        $ob = \Bitrix\Catalog\PriceTable::getList(array(
            'filter' => array(
                'PRODUCT_ID' => [$arParams['PRODUCT_ID']],
                'CATALOG_GROUP_ID' => $arParams['CATALOG_GROUP_ID']
            ),
            'select' => array(
                'ID',
                'PRICE',
                'PRODUCT_ID',
                'CURRENCY',
                'CATALOG_GROUP_ID',
            ),
        ));
        while ($price = $ob->Fetch()) {
            static::$old_prices = $price['PRICE'];
        }
    }

    public static function iblockUpdateEnd($id,&$arParams){
        if($arParams['PRICE'] != static::$old_prices){
            $siteId = CIBlockElement::GetByID($arParams['PRODUCT_ID'])->Fetch()['LID'];
            if(!empty($arParams['CATALOG_GROUP_ID']))
            {
                static::run($siteId,$arParams['CATALOG_GROUP_ID']);
            }
        }
    }

    public static function run($siteId,$catalogGroupId){
        global $DB;
        $resObj = $DB->Query('SELECT site_id, catalog_group_id FROM FeedStack');
        $feedStack = [];
        while ($res = $resObj->Fetch()){
            $feedStack[] = $res;
        }
        if(!in_array(['site_id' => $siteId, 'catalog_group_id' => $catalogGroupId], $feedStack)){
            $DB->Query('INSERT INTO FeedStack SET site_id = "'.$siteId.'", catalog_group_id = "'.$catalogGroupId.'"');
        }
    }
}