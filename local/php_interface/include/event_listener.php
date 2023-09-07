<?
//stop add discount from basket rules for product 
//if this product have discount from discount product
// AddEventHandler(
//    'catalog',
//    'OnGetDiscountResult',
//    ['stopApplyBasketRules','run']
// );

// AddEventHandler(
//    'sale',
//    'OnBasketUpdate',
//    ['stopApplyBasketRules','coupon']
// );

// AddEventHandler(
//    'sale',
//    'OnSetCouponList',
//    ['stopApplyBasketRules','run']
// );


//this group listers for add site to update YandexFeed
 AddEventHandler(
    'catalog',
    'OnDiscountUpdate',
    ['FeedRun','DiscountChange']
 );
 
 AddEventHandler(
    'catalog',
    'OnDiscountAdd',
    ['FeedRun','DiscountChange']
 );

 AddEventHandler(
    'catalog',
    'OnBeforeDiscountDelete',
    ['FeedRun','DiscountChange']
 );

 AddEventHandler(
    'catalog',
    'OnBeforePriceUpdate',
    ['FeedRun','iblockUpdatePre']
 );

 AddEventHandler(
    'catalog',
    'OnPriceUpdate',
    ['FeedRun','iblockUpdateEnd']
 );

//this eventListener for correct sale kit
AddEventHandler(
    'catalog',
    'OnGetOptimalPrice',
    ['customGetOptimalPrice','run']
 );

