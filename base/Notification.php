<?php
class Notification{
  public function sendNotification($receiver, $title, $body, $elementId, $type){
    $exito = false;
    $url = "https://fcm.googleapis.com/fcm/send";
    $server_key = "AAAA-TT5zxU:APA91bFee1GAudrQyAUs11BgHDu0Fmfu3p8aS1sEQa_2Bxr34-4ORyNhf5O3nV_5jguJ5xvIduSxF8bQs5_wZy4hn6lzVJG3QzqZRGIcgF6O9ZAcBvnKYvRZE9JMXI7zdf6CxPpaYkx2";
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
      $exito = 'Error al enviar notificación' . curl_error($ch);
    } else {
      $exito = true;
    }
    curl_close($ch);
    return $exito;
  }

  public function sendNotificationTopic(&$sError, $topic, $title, $body, $elementId, $type){
    $exito = false;
    $url = "https://fcm.googleapis.com/fcm/send";
    $server_key = "AAAA-TT5zxU:APA91bFee1GAudrQyAUs11BgHDu0Fmfu3p8aS1sEQa_2Bxr34-4ORyNhf5O3nV_5jguJ5xvIduSxF8bQs5_wZy4hn6lzVJG3QzqZRGIcgF6O9ZAcBvnKYvRZE9JMXI7zdf6CxPpaYkx2";
    $headers = array(
      'Content-Type:application/json',
      'Authorization:key='.$server_key
    );

    $payload = array(
      "to" => "/topics/".$topic,
      //"to" => "excmecxLnSw:APA91bGHdjNnX09sSmSO2Rzd12nV2Ftlx2OVBj8nJNcpljVV0SmLdleXiTsJyJ9oKsl58gK-Iq-VsrM2mAoS6EU9lgpJ_1Gznj2zA29_KuqAOn440FyGsP6T9c9hKKJeEvUeO8Iw32fx",
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

    /*
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
      $sError = 'Error al enviar notificación' . curl_error($ch);
    } else {
      $exito = true;
      //$sError .= "Se mandó".json_encode($result);
    }
    curl_close($ch);
*/
    return $exito;
  }

}
?>