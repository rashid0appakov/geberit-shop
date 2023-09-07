<!--noindex-->
						<div class="sort-cover">
							<div class="goods__card-sort-filter">
								<span>Фильтры <div class="tag is-warning hide">!</div></span>
							</div>
							<div class="goods__card-sort-filter-button hide mobile-show">
								<div class="sel sel__placeholder--black-panther">
									<select name="catalog-sort">
										<? foreach(CClass::$arMobileSortFields AS $key => &$arSort):
											foreach($arSort AS $k => &$order):
												$selected = ($arParams['SORT'] == $key && $arParams['ORDER'] == $order ? ' selected="selected"' : '');
												if (!key_exists($arParams['SORT'], CClass::$arMobileSortFields) || !in_array($order, ['asc', 'desc']) &&
													$key == 'rating')
													$selected = ' selected="selected"';
										?>
										<option value="<?=$APPLICATION->GetCurPageParam("sort=$key&order=$order", array("sort", "order"))?>"<?=$selected?>><?=GetMessage('HDR_SORT_'.strtoupper($key.'_'.$order))?></option>
										<?
											endforeach;
										endforeach;?>
									</select>
								</div>
							</div>
						</div>
						<? if (!empty(CClass::$arSortFields) or $arParams['MOBILE']):?>
							<script type="text/javascript">
								function target_href(target){
									window.location.href = target;
								}
							</script>
							<div class="goods__card-sort card-sort">
								<? if (!empty(CClass::$arSortFields)):?>
									<div class="card-sort__categories hide-mobile">
										<span>Сортировать:</span>
										<? foreach(CClass::$arSortFields AS $key => &$title):
											if ($arParams['SORT'] == $key){
												if ($arParams['ORDER'] == "asc"){?>
													<a class="active asc sorting" href="javascript:;" onclick="target_href('<?=$APPLICATION->GetCurPageParam("sort=$key&order=desc", array("sort", "order"))?>');" ><?=$title?></a>
												<?}else{?>
													<a class="active desc sorting" href="javascript:;" onclick="target_href('<?=$APPLICATION->GetCurPageParam("sort=$key&order=asc", array("sort", "order"))?>');"><?=$title?></a>
												<?}
											}else{?>
												<a href="javascript:;" class="sorting"  onclick="target_href('<?=$APPLICATION->GetCurPageParam("sort=$key&order=asc", array("sort", "order"))?>');"><?=$title?></a>
											<?};
										endforeach;?>
									</div>
								<? endif;?>
								<div class="toggle-button-cover_list">
									<div class="short short-desc-off">
										Подробно
									</div>
									<div class="button-cover_list">
										<div class="button_list r" id="button-1">
											<input type="checkbox" class="checkbox_list short-desc-box">
											<div class="knobs_list"></div>
											<div class="layer_list"></div>
										</div>
									</div>
									<div class="detail not_active short-desc-on">
										Кратко
									</div>
								</div>
								<?/*if(!$arParams['MOBILE']):?>
									<div class="card-count__categories hide-mobile">
										<span>Показать</span>
										<select class="count_catalog_items" data-sort="<?=$arParams['SORT']?>" data-order="<?=$arParams['ORDER']?>">
										<?
										$arPerPage = [
											12, 24, 48
										];
										if(!empty($_REQUEST['pp']) and in_array($_REQUEST['pp'], $arPerPage))
										{
											$_SESSION['CATALOG_PP'] = $_REQUEST['pp'];
										}
										foreach($arPerPage AS $pp):
											?>
												<option value="<?=$pp?>"<?if($_SESSION['CATALOG_PP'] == $pp):?> selected<?endif?>><?=$pp?></option>
											<?
										endforeach;?>
										</select>
										<span>из <?$APPLICATION->ShowViewContent('all_catalog_items');?> товаров.</span>
									</div>
								<?endif;*/?>
							</div>
						<? endif;?>
						<div class="clear"></div>
<!--/noindex-->
