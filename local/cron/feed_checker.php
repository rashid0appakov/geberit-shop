
<?

function tgSend($message) {
                                                                                                                                                                                         
  $botToken="AAELGmZMW6_Hev_rA7-v7IrOVS0N4kLHCCE";

  $website="https://api.telegram.org/bot".$botToken;
  $chatId=119597116;  //** ===>>>NOTE: this chatId MUST be the chat_id of a person, NOT another bot chatId !!!**
  $params=[
      'chat_id'=>$chatId, 
      'text'=>$message,
  ];
  $ch = curl_init($website . '/getUpdates');
  curl_setopt($ch, CURLOPT_HEADER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $result = curl_exec($ch);
  curl_close($ch);

var_dump($result);

}


tgSend('test');

?>

 