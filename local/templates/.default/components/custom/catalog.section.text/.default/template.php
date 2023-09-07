<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if (!empty($arResult["TEXT"])):?>
<p class="goods__title-description" id="sectionDescription"><?=$arResult["TEXT"]?></p>
<a href="javascript:void(0)" class="goods__title-link" id="moreDescriptionBtn">Подробнее</a>

<script>
	$(document).ready(function(){
		var descrFull = $('#sectionDescription').text();
		var splitDescr = descrFull.split('//');
		var descrFirst = splitDescr[0];
		descrFull = splitDescr.join('');

		$('#sectionDescription').text(descrFirst);

		$('#moreDescriptionBtn').on('click', function() {
			if ($(this).hasClass('expanded')) {
				$('#sectionDescription').text(descrFirst);
				$(this).removeClass('expanded');
			} else {
				$('#sectionDescription').text(descrFull);
				$(this).addClass('expanded');
			}
		});
	});
</script>
<?endif;