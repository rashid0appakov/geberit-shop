<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//pr($arParams['RESULT']);
?>
<script type="text/javascript">
  (function () {
    function readCookie(name) {
      if (document.cookie.length > 0) {
        offset = document.cookie.indexOf(name + "=");
        if (offset != -1) {
          offset = offset + name.length + 1;
          tail = document.cookie.indexOf(";", offset);
          if (tail == -1) tail = document.cookie.length;
          return unescape(document.cookie.substring(offset, tail));
        }
      }
      return null;
    }

    var lh_clid = '5ae1a4a8e694aa5df4e08b16';
    var order_id = '<?=$arParams['RESULT']['ACCOUNT_NUMBER']?>'; /* код заказа */
    var cart_sum = '<?=number_format($arParams['RESULT']['PRICE'], 2, '.', '');?>'; /* сумма заказа в формате 200.00 - разделитель точка и 2 знака после точки */
<?
$arOffers = [];

foreach($arParams['RESULT']['BASKET'] as $arOffer)
{
	$arOffers[] = "{
		'url': '//".$_SERVER['HTTP_HOST'].$arOffer['DETAIL_PAGE_URL']."',
		'name': '".str_replace("'", "", $arOffer['NAME'])."',
		'price': '".number_format($arOffer['PRICE'], 2, '.', '')."',
		'count': '".$arOffer['QUANTITY']."',
		'currency' : '".$arOffer['CURRENCY']."'
	}";
}
?>
    var order_offers = [<?=implode(",", $arOffers);?>] /* товары в заказе */

    var uid = readCookie('_lhtm_u');
    var vid = readCookie('_lhtm_r').split('|')[1];
    var url = encodeURIComponent(window.location.href);
    var path = "https://track.leadhit.io/stat/lead_form?f_orderid=" + order_id + "&url=" + url + "&action=lh_orderid&uid=" + uid + "&vid=" + vid + "&ref=direct&f_cart_sum=" + cart_sum + "&clid=" + lh_clid;

    var sc = document.createElement("script");
    sc.type = 'text/javascript';
    var headID = document.getElementsByTagName("head")[0];
    sc.src = path;
    headID.appendChild(sc);

    if (Array.isArray(order_offers) && order_offers.length > 0) {
      var requestBody = {
        'order_id': order_id,
        'cart_sum': cart_sum,
        'vid': vid,
        'uid': uid,
        'clid': lh_clid,
        'offers': order_offers
      }
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'https://track.leadhit.io/stat/lead_order', true);
      xhr.setRequestHeader('Content-type', 'application/json; charset=utf-8');
      xhr.onreadystatechange = function () {
        if (this.readyState != 4) return
        console.log('order sended')
      }
      xhr.send(JSON.stringify(requestBody));
    }
  })();
</script>