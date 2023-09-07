<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$uid = uniqid();

$jsParams = array(
	"uid" => $uid,
	"ajaxUrl" => $templateFolder."/ajax.php",
	"searchUrl" => "/search/",
);?>
<script type="text/javascript">
	window.SearchHeader = new JSSearchHeader(<?=json_encode($jsParams)?>);
</script>
<div class="search-field" id="search_<?=$uid?>">
	<div class="header-submit"></div>

	<input type="text" id="input_<?=$uid?>" class="input" name="q" value="<?=$_REQUEST['q']?>" autocomplete="off" placeholder="Поиск товаров" />
	<div id="popup_<?=$uid?>" class="search-menu-popup2"></div>
</div>