<? if( count($arResult['PROPERTIES']['VIDEO']['VALUE']) > 0 && is_array($arResult['PROPERTIES']['VIDEO']['VALUE']) ) { ?>

	<div class="container is-widescreen goods__tabs--wrap" id="list-video">
		<div class="level is-mobile carousel__title">
			<div class="level-left">
				<h2 class="is-size-3" style="margin-top: 0;">Видеообзор</h2>
			</div>
		</div>

		<div class="content videos">
		
			<?
			$count[1] = array(
								"WIDTH" => '1050',
								"HEIGHT" => '700',
								);
			$count[2] = array(
								"WIDTH" => '600',
								"HEIGHT" => '400',
								);
			$count[3] = array(
								"WIDTH" => '550',
								"HEIGHT" => '300',
								);
								
			$width = $count[ count( $arResult['PROPERTIES']['VIDEO']['VALUE'] ) ]['WIDTH'];				
			$height = $count[ count( $arResult['PROPERTIES']['VIDEO']['VALUE'] ) ]['HEIGHT'];				
			?>

			<? foreach( $arResult['PROPERTIES']['VIDEO']['VALUE'] as $val ) { ?>
			
				<?
				$exp = explode('v=', $val);
				?>
			
				<div class="item-video" style="width: 100%;" data-src="https://www.youtube.com/embed/<?=$exp[1];?>">
                    <?/*/?>
					<iframe
                        loading="lazy" 
						width="<?=$width;?>" 
						height="<?=$height;?>" 
						src="https://www.youtube.com/embed/<?=$exp[1];?>" 
						frameborder="0" 
						allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" 
						allowfullscreen>
					</iframe>
                    <?/**/?>
				</div>
			
			<? } ?>
			
			<div style="clear: both;"></div>
		
		</div>
	</div>

	<br/>
	<br/>
<?/**/?>
<script type="text/javascript">
    $(document).ready(function () {
        $(".item-video").each(function(index){
            var src = $(this).attr("data-src");
            $(this).append('<iframe loading="lazy" width="<?=$width;?>" height="<?=$height;?>" src="'+src+'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen> </iframe>');
        });
    });
</script>
<?/**/?>

<? } ?>
