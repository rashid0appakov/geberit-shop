<?if($GLOBALS['USER']->IsAdmin()||true){?>
	<?
		$video['link'] = 'https://www.youtube.com/embed/F4NGq9JJKaw';
		preg_match('#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#', $video['link'], $matche);
		if(isset($matche[2]) && $matche[2] != '') { 
			$video['id'] = $matche[2]; 
			$video['image'] = 'https://img.youtube.com/vi/' . $video['id'] . '/maxresdefault.jpg'; 
		}
	?>
	<div id="player-<?=randString(8)?>-<?=$video['id']?>" class="lazy-video"  data-src-id="<?=$video['id']?>" style="position:relative">
		<img src="<?=$video['image']?>">
		<svg height="100%" version="1.1" viewBox="0 0 68 48" width="100%" style="position:absolute;left:50%;top:50%;width:12%;height:15%;transform:translateX(-50%) translateY(-50%)">
			<path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path>
			<path d="M 45,24 27,14 27,34" fill="#fff"></path>
		</svg>
		<script>
			$(function(){
				if(!window.ytIframeData) { 
					window.ytIframeData = {};
					window.onYouTubeIframeAPIReady = function() { 
						window.ytPlayer = {};
						window.createYTPlayer = function(key, value) {
							ytPlayer[key] = new YT.Player(key, {
								width: value.width,
								height: value.height,
								videoId: value.id,
								playerVars: { 'autoplay': 1, 'playsinline': 1 },
								events: { 'onReady': (e)=>{ e.target.playVideo(); } }
							});
						};
						$.each(ytIframeData,createYTPlayer); 
					};
				}

				$(".lazy-video").click(function(){
					var key = $(this).attr('id');
					ytIframeData[key] = {id:$(this).attr('data-src-id'),width:$(this).width(),height:$(this).height()};
				
					if(!window.YT){
						var tag = document.createElement('script');
						tag.src = "https://www.youtube.com/iframe_api";
						var firstScriptTag = document.getElementsByTagName('script')[0];
						firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
					} else {
						createYTPlayer(key,ytIframeData[key]);
					}
				});
			});
		</script>
	</div>
	<?return;?>
<?}?>
<iframe width="560" height="315" src="https://www.youtube.com/embed/F4NGq9JJKaw" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
