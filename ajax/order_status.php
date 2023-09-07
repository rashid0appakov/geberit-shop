<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock"))
	return;



if( $_GET['id'] ) {
	
	if (!($arOrder = CSaleOrder::GetByID( $_GET['id'] )))
	{
	   echo "Заказ с кодом ".$_GET['id']." не найден";
	}
	else
	{
	   /*echo "<pre>";
	   print_r($arOrder);
	   echo "</pre>";*/
	   
	   
	   $arStatus = CSaleStatus::GetByID( $arOrder['STATUS_ID'] );
	   $arPaySys = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID'], 1);
	   
	   
	}
	
}
?>

<? if( $arOrder ) { ?>

	<div class="columns">
		<div class="column is-7">
			<div class="columns">
				<div class="column">
					<span class="boldText">
						Статус:
					</span>
					<span class="boldText">
						Сумма к оплате:
					</span>
					<span class="boldText">
						Способ оплаты:
					</span>
				</div>
				<div class="column">
					<span>
						<?=$arStatus['NAME'];?>
					</span>
					<span>
						<?=number_format($arOrder['PRICE'], 0, '', ' ');?> руб.
					</span>
					<span>
						<?=$arPaySys['NAME'];?>
					</span>
				</div>
			</div>
		</div>
		
		<!--
		<div class="column is-6">
			<div class="columns">
				<div class="column">
					<span class="boldText">
						Ваш менеджер:
					</span>
					<span class="boldText">
						Телефон:
					</span>
				</div>
				<div class="column">
					<span>
						Иван Птеров
					</span>
					<span>
					   (495) 123-1201 доб. 124
					</span>
				</div>
			</div>
		</div>
		-->
		
	</div>

<? } else { ?>

	<?/*?>
	<div class="columns">
		
		Заказ #<?=$_GET['id'];?> не найден.
		
	</div>	
	<?*/?>

<? } ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>