<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("showroom");

?>
<script src="js/slick.min.js"></script>
<script src="js/main.js"></script>
<link rel="stylesheet" href="css/slick-theme.min.css">
<link rel="stylesheet" href="css/showroom.css">
<?
$show_room_ib = 120;
$arSelect = Array("ID", "NAME", "DETAIL_PICTURE");
$arFilter = Array("IBLOCK_ID"=>$show_room_ib, "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
$res = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilter, false, false, $arSelect);
$arr = array();
while($ob = $res->GetNextElement())
{
	$arFields = $ob->GetFields();
	$file = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width'=>90, 'height'=>60), BX_RESIZE_IMAGE_PROPORTIONAL, false);   
	$DETAIL_PICTURE = CFile::ResizeImageGet($arFields['DETAIL_PICTURE'], array('width'=>1171, 'height'=>780), BX_RESIZE_IMAGE_EXACT, false);   

	$arr[] = array('NAME' => $arFields['NAME'], 'SMALL' => $file['src'], 'BIG' => $DETAIL_PICTURE['src']);
}

if (is_array($arr) && count(arr)>0){
?>
<div class="container">
	<div class="showroom"> 
		<div class="adress">г. Москва Дубнинская ул., дом 75 Б стр. 2  </div>
		<div class="title">Магазин швейцарской сантехники от Geberit</div>
        <h1 style="position:absolute;top:-2000px;opacity:0;"><?=$APPLICATION->ShowTitle(false)?></h1>
		<div class="schedule">Ежедневно c 09:00 до 21:00</div>
		<div class="slideshow">
			<div class="slideshowBig">
				<?
				foreach ($arr as $ar){
					?>
				<div class="item">
					<img src="<?=$ar['BIG']?>" alt="<?=$ar['NAME']?>">
				</div>
					<?
				}
				?>
			</div>
			<div class="slideshowMini">
				<?
				foreach ($arr as $ar){
					?>
				<div class="item">
					<img src="<?=$ar['SMALL']?>" alt="<?=$ar['NAME']?>">
				</div>
					<?
				}
				?>
			</div>
		</div>
	</div>
	<div class="showroom-products">
		<div class="title">Товары в наличии в магазине</div>
		<?$APPLICATION->IncludeComponent(
		    "bitrix:main.include",
		    "",
		    array(
		        "AREA_FILE_SHOW" => "file",
		        "PATH" => SITE_DIR."include/main/showroom_new.php"
		    ),
		    false,
		    array("HIDE_ICONS" => "Y")
		);?>
		<br><br>
<?/*
		<div class="pagination-wrapper">
			<div class="card-cell__show-more">
				<a href="#">
					<p>Показать еще (+<span>7</span>)</p>
				</a>
			</div>
		</div>
*/?>
	</div>
<?
if ($_GET['PAGEN_1']>0){
	?>
	<script>
        $(document).ready(function (){
            $('html, body').animate({
                scrollTop: $(".showroom-products").offset().top
            }, 1000);
        });
    </script>
	<?
}
?>

</div>
<?
}
?>		

</div>
<?
$h1 = 'Магазин сантехники {brand}';
$title = 'Магазин сантехники {brand} в {топоним}';
$description = 'Сантехника {brand} в магазине в {топоним}. В магазине представлена оригинальная сантехника производителя {brand} по адресу {адрес}';
$arrTags = [
    '{brand}' => 'Geberit',
    '{топоним}' => 'Москве',
    '{адрес}' => 'г. Москва Дубнинская ул., дом 75 Б стр. 2',
];

$APPLICATION->SetTitle(str_replace(array_keys($arrTags), $arrTags, $h1));
$APPLICATION->SetPageProperty('title', str_replace(array_keys($arrTags), $arrTags, $title));
$APPLICATION->SetPageProperty('description', str_replace(array_keys($arrTags), $arrTags, $description));
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>