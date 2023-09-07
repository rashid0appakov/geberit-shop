<?
if(array_key_exists('message', $_POST) and !empty($_POST['message']))
{
	$data = array(
		"error" => true,
		"message" => "Почтальон недоступен",
	);
	$APPLICATION->RestartBuffer();
	header('Content-Type: application/json');
	die(json_encode($data));
}