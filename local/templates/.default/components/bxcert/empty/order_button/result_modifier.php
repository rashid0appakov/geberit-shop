<?
function getCountBasket()
{
  CModule::IncludeModule("sale");
  
  return CSaleBasket::GetList(false, array("FUSER_ID" => CSaleBasket::GetBasketUserID(),"LID" => SITE_ID,"ORDER_ID" => "NULL"),false,false,array("ID" ))->SelectedRowsCount();
}

?>