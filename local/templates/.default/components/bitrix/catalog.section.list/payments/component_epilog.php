<?
if($_REQUEST['tab'] > 0){
	?>
	<script type="text/javascript">
		$(document).ready(function () {
			$(".js-tabs-title[data-tab='#tab-<?=$_REQUEST['tab']?>']").trigger('click');
		});
	</script>
	<?
}
?>