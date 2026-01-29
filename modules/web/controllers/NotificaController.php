<?php
  class NotificaController extends Controller{
    
    public function actionInit(){
    }

    public function actionSaludoMatutino(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

     
      $rows = User::model()->findAll("WHERE app_fcm_token IS NOT NULL");

      foreach ($rows as $key => $value) {
        $rec = new Reclutador("WHERE user = ".$value->id);
        $numprospectos = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE reclutador_user = ".$value->id)[0]->total;
  
        $titulo = "¡Buenos días ".$rec->nombre."!";
        $texto = "Te deseamos mucho éxito en el día. \nNo olvides atender a los ".$numprospectos." prospectos que tienes asignados.";

        if(date('w', time()) != 0 && date('w', time()) != 6){
          $noti = new Notification;
          $res = $noti->sendNotification($value->app_fcm_token, $titulo, $texto, "", "general");
          $aSalida["demo"] = $res;
          if($res){
            $exitosos += 1;
          } else {
            $errores += 1;
          }
        }
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }

    public function actionSaludoMedioDia(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

     
      //$rows = User::model()->findAll("WHERE id IN(1)");
      $rows = User::model()->findAll("WHERE app_fcm_token IS NOT NULL");

      foreach ($rows as $key => $value) {
        $rec = new Reclutador("WHERE user = ".$value->id);
        $auxWhere = "";
        if($value->id != 1){
          $auxWhere = "AND plaza IN(".$rec->plazas.")"; 
        }

        $numprospectos = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE reclutador_user IS NULL ".$auxWhere)[0]->total;
  
        $titulo = "¡Hola ".$rec->nombre."!";
        $texto = "Hay ".$numprospectos." prospectos sin asignar en tu zona. \nAsígnalos y sigue con tus buenos números.";


        if(date('w', time()) != 0 && date('w', time()) != 6){
          $noti = new Notification;
          $res = $noti->sendNotification($value->app_fcm_token, $titulo, $texto, "", "general");
          $aSalida["demo"] = $res;
          if($res){
            $exitosos += 1;
          } else {
            $errores += 1;
          }
        }
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }

    public function actionResumenDia(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

     
      //$rows = User::model()->findAll("WHERE id IN(1)");
      $rows = User::model()->findAll("WHERE app_fcm_token IS NOT NULL");

      foreach ($rows as $key => $value) {
        $rec = new Reclutador("WHERE user = ".$value->id);
        $auxWhere = "";
        if($value->id != 1){
          $auxWhere = "AND plaza IN(".$rec->plazas.")"; 
        }

        $atendidos = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE reclutador_user = ".$value->id." AND DATE_FORMAT(modified, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')")[0]->total;
        $asignados = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE reclutador_user = ".$value->id." AND DATE_FORMAT(fecha_asignado, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')")[0]->total;
        $contratados = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE reclutador_user = ".$value->id." AND DATE_FORMAT(fecha_contratado, '%Y-%m-%d') = DATE_FORMAT(now(), '%Y-%m-%d')")[0]->total;
  
        $titulo = "Aquí tienes tu resumen del día: ";
        $texto = $atendidos." prospectos atendidos.\n";
        $texto .= $asignados." nuevos prospectos asignados.\n";
        $texto .= $contratados." prospectos contratados.\n";
        $texto .= "¡FELICIDADES!";


        if(date('w', time()) != 0 && date('w', time()) != 6){
          $noti = new Notification;
          $res = $noti->sendNotification($value->app_fcm_token, $titulo, $texto, "", "general");
          $aSalida["demo"] = $res;
          if($res){
            $exitosos += 1;
          } else {
            $errores += 1;
          }
        }
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }


    public function actionEnviaNotificacion(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

      $aWhere = "AND (";
      $sep = "";
      $plazas = $this->params["plaza"];
      foreach ($plazas as $key => $value) {
        $aWhere .= $sep." FIND_IN_SET('$value', plazas) ";
        $sep = "OR";
      }
      if(count($plazas) == 0){
        $aWhere .= "1=1";
      }
      $aWhere .= ")";

      $rows = Reclutador::model()->executeQuery("SELECT r.* FROM ec_reclutador r LEFT JOIN user u ON r.user = u.id WHERE u.app_fcm_token IS NOT NULL ".$aWhere);

      foreach ($rows as $key => $rec) {
        $user = new User($rec->user);
        if($user->app_fcm_token != ""){
          $titulo = $this->params["titulo"];
          $texto = $this->params["mensaje"];

          if(date('w', time()) != 0 && date('w', time()) != 6){
            $noti = new Notification;
            $res = $noti->sendNotification($user->app_fcm_token, $titulo, $texto, "", "general");
            if($res){
              $exitosos += 1;
            } else {
              $errores += 1;
            }
          }
          
        }
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }

    public function actionEnviaNotificacionReclutadores(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;
      $ids = implode(",", $this->params["reclutadores"]);
      $rows = Reclutador::model()->executeQuery("SELECT r.* FROM ec_reclutador r LEFT JOIN user u ON r.user = u.id WHERE u.app_fcm_token IS NOT NULL AND r.id IN($ids)");

      foreach ($rows as $key => $rec) {
        $user = new User($rec->user);
        if($user->app_fcm_token != ""){
          $titulo = $this->params["titulo"];
          $texto = $this->params["mensaje"];

          if(date('w', time()) != 0 && date('w', time()) != 6){
            $noti = new Notification;
            $res = $noti->sendNotification($user->app_fcm_token, $titulo, $texto, "", "general");
            if($res){
              $exitosos += 1;
            } else {
              $errores += 1;
            }
          }
          
        }
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }

    public function actionCumpleanos(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

     
      //$rows = User::model()->findAll("WHERE id IN(1)");
      $rows = Reclutador::model()->findAll("WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') = DATE_FORMAT(now(), '%m-%d')");
      //$this->error = $rows;
      foreach ($rows as $key => $value) {
        $user = new User($value->user);
     
        $titulo = "¡Feliz Cumpleaños, ".$value->nombre."! ";
        $texto = $atendidos." Te deseamos lo mejor en tu día.\n";
        $texto .= $contratados." Recibe un abrazo de parte de toda la familia OXXO.\n";

        if($user->app_fcm_token != ""){
          if(date('w', time()) != 0 && date('w', time()) != 6){
            $noti = new Notification;
            $res = $noti->sendNotification($value->app_fcm_token, $titulo, $texto, "", "general");
            $aSalida["demo"] = $res;
            if($res){
              $exitosos += 1;
            } else {
              $errores += 1;
            }
          }
        }
      
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }



    public function actionRecordatorioPaquetes(){

      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;

     
      $rows = User::model()->findAll("WHERE rol IN(1,7)");
      //$rows = Reclutador::model()->findAll("WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') = DATE_FORMAT(now(), '%m-%d')");
      //$this->error = $rows;
      foreach ($rows as $key => $user) {
        $value = new Reclutador("WHERE user = ".$user->id);
     
        $titulo = "Buenas tardes ".$value->nombre." ";
        $texto = "No olvides adquirir tu paquete de publicaciones para el próximo mes.\n";
        $texto .= "Recuerda que si no eliges ninguno, se te asignará el paquete básico o el último que tengas activo.\n";

        if($user->app_fcm_token != ""){
            $noti = new Notification;
            $res = $noti->sendNotification($value->app_fcm_token, $titulo, $texto, "", "general");
            $aSalida["demo"] = $res;
            if($res){
              $exitosos += 1;
            } else {
              $errores += 1;
            }
          
        }
      
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $this->renderJSON($aSalida);
    }

    public function actionRecordatorioPaquetesMail(){


      require('lib/Mailer/PHPMailerAutoload.php');
 
      $this->template = null;
      
      $aSalida = array();
      $sError = "";
      $errores = 0;
      $exitosos = 0;


     
      $rows = User::model()->findAll("WHERE rol IN(1,7)");
      //$rows = Reclutador::model()->findAll("WHERE DATE_FORMAT(fecha_nacimiento, '%m-%d') = DATE_FORMAT(now(), '%m-%d')");
      //$this->error = $rows;
      foreach ($rows as $key => $user) {
        $value = new Reclutador("WHERE user = ".$user->id);
     
        $titulo = "Buenas tardes ".$value->nombre." ";
        $texto = "No olvides adquirir tu paquete de publicaciones para el próximo mes.<br>";
        $texto .= "Recuerda que si no eliges ninguno, se te asignará el paquete básico o el último que tengas activo.<br>";

        if($user->username != ""){
            
            $to = $user->username;
            if($to == "admin"){
              $to = "yrodrigocorn@icloud.com";
            }
            //
            $subject = "Adquiere tu paquete de publicaciones";
            $sHtml = file_get_contents("https://".$this->burl."templates/mailing/notificacion.tpl.php");
            $sHtml = str_replace("##titulo##", $titulo , $sHtml);
            $sHtml = str_replace("##mensaje##", $texto , $sHtml);
            $sHtml = str_replace("##tituloBoton##", "ENTRAR" , $sHtml);

            $sHtml = str_replace("##botonUrl##", "https://".$this->burl."ecom/publicidadpaqueteplaza" , $sHtml);
            $sHtml = str_replace("##url##", "https://".$this->burl."ecom/publicidadpaqueteplaza" , $sHtml);
           
            
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
            $mail->Username = 'AKIAINUBGIFNP325UFHA';
            $mail->Password = 'Aumq4i/jLhYgljc5zqnyvCylbiwtxOKGhGh7yDPfvYxe';
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('oxxo_reop@cuarto101.mx', 'OXXO REOP');
            $mail->Subject = $subject;
            $mail->addAddress($to, $value->nombre);
            $mail->isHTML(true);
            $mail->Body = $sHtml;

            if(!$mail->send()) {
              $errores += 1;
              $sError .= 'Message could not be sent.';
              $sError .= 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
              $exitosos += 1;
            }
          
        }
      
        $aSalida["aux"] = $numprospectos;
      }

      $aSalida["exitosos"] = $exitosos;
      $aSalida["errores"] = $errores;
      $aSalida["debug"] = $sError;
      $this->renderJSON($aSalida);
    }

  }
?>