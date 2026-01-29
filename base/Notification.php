<?php
class Notification{
  public function sendNotification($receiver, $title, $body, $elementId, $type){
    $exito = false;
    $url = "https://fcm.googleapis.com/fcm/send";
    $server_key = Env::get("FCM_SERVER_KEY");
    $headers = array(
      'Content-Type:application/json',
      'Authorization:key='.$server_key
    );

    $payload = array(
      "to" => $receiver,
      "notification" => array(
        "title"         => $title,
        "body"          => $body,
        "icon"          => "ic_launcher",
        "click_action"  => "FLUTTER_NOTIFICATION_CLICK"
      ),
      "data" => array(
        "title"     => $title,
        "body"      => $body,
        "elementId" => $elementId,
        "type"      => $type
      )
    );


    $data = json_encode($payload);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $result = curl_exec($ch);
    if ($result === FALSE) {
      $exito = 'Error al enviar notificacion' . curl_error($ch);
    } else {
      $exito = true;
    }
    curl_close($ch);
    return $exito;
  }

  public function sendNotificationTopic(&$sError, $topic, $title, $body, $elementId, $type){
    $exito = false;
    $url = "https://fcm.googleapis.com/fcm/send";
    $server_key = Env::get("FCM_SERVER_KEY");
    $headers = array(
      'Content-Type:application/json',
      'Authorization:key='.$server_key
    );

    $payload = array(
      "to" => "/topics/".$topic,
      "notification" => array(
        "title"         => $title,
        "body"          => $body,
        "icon"          => "ic_launcher",
        "click_action"  => "FLUTTER_NOTIFICATION_CLICK"
      ),
      "data" => array(
        "title"     => $title,
        "body"      => $body,
        "elementId" => $elementId,
        "type"      => $type
      )
    );

    return $exito;
  }

}
?>
