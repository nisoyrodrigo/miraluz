<?php
  class Controller{
    
    public $campos = array();
    public $callback;
    public $camposFaltantes = array();
    public $params = "";
    public $error = "";
    public $configuracion;
    public $template;
    public $module;
    public $burl;
    public $murl;
    public $content;
    public $user;
    public $intefaz;
    
    public function __construct($params){
      require 'config/config.php';

      $this->params = $params;
      $this->configuracion = $config;

      if(isset($this->params["murl"])){
        $this->murl = $this->params["murl"];
      }

      if(isset($this->params["module"])){
        $this->module = $params["module"];
      }

      if(isset($this->params["burl"]))
        $this->burl = $this->params["burl"];
      
      if(!empty($this->module)){
        $this->template = isset($config["modules"][$this->module]["template"]) ? $config["modules"][$this->module]["template"] : "";
      }
      $this->interfaz = count(explode("\\", get_called_class()) > 0) ? str_replace("controller", "", strtolower(explode("\\", get_called_class())[0])):"";

      unset($params["burl"]);
      unset($params["murl"]);
      unset($params["module"]);
      
      $this->user = Motor::app()->getUser($this->module);
    }
    
    public function setUser($user){
      Motor::app()->setUser($this->module, $user);
    }
    
    public function userLogout(){
      Motor::app()->userLogout($this->module);
    }
    
    public function render($layout, $params = null, $absolute = false){
      
      if(is_array($params)){
        foreach($params as $variable => $valor){
          $$variable = $valor;
        }
      }
      
      $namespace = explode("\\", get_called_class());
      $controller = $namespace[count($namespace)-1];
      
      if($absolute)
        $view = Motor::app()->absolute_url.$layout.".tpl.php";
      else
        $view = Motor::app()->absolute_url.$this->murl."/views/".str_replace("controller", "", strtolower($controller))."/".$layout.".tpl.php";
        
      

      if(!file_exists($view))
        echo "No existe el archivo de vista: ".$view;
      
      $burl = $this->burl;
      $murl = $this->murl;
      $user = $this->user;
      $module = $this->module;
      
      include "base/Functions.php";    
      
      ob_start();
      include $view;
      $content = ob_get_contents();
      ob_end_clean();

      if(!empty($this->template)){
        include $this->murl."/templates/".$this->template.".tpl.php";
      }
      else{
        print $content;
      }
        
    }
    
    public function renderByString($content){
      
      $burl = $this->burl;
      $murl = $this->murl;
      $user = $this->user;
      $module = $this->module;
      $functions = Motor::app()->absolute_url."base/Functions.php";
      
      if(!empty($this->template)){
        include $functions;
        include Motor::app()->absolute_url.$this->murl."/templates/".$this->template.".tpl.php";
      }
      else{
        print $content;
      }


        
    }
    
    public function renderAbsoluteUrl($layout, $params = null){

      if(is_array($params)){
        foreach($params as $variable => $valor){
          $$variable = $valor;
        }
      }
      
      $namespace = explode("\\", get_called_class());
      $controller = $namespace[count($namespace)-1];
      
      $view = $layout.".tpl.php";

      if(!file_exists($view))
        echo "No existe el archivo de vista: ".$view;
      
      $burl = $this->burl;
      $murl = $this->murl;
      $user = $this->user;
      $module = $this->module;
      
      include "base/Functions.php";    
      
      ob_start();
      include $view;
      $content = ob_get_contents();
      ob_end_clean();

      if(!empty($this->template)){
        include $this->murl."/templates/".$this->template.".tpl.php";
      }
      else{
        print $content;
      }
        
    }
    
    public function renderJSON($data = null){
      if(isset($_GET["callback"]))
        $this->callback = $_GET["callback"];
      
      if($data == null || $data == ""){
        $data = array();
      }
      if($this->error == ""){
        if(empty($this->callback)){
          header('Content-Type: application/json;charset=utf-8;');
          echo json_encode($data);
        }
        else{
          header('Content-Type: application/json;charset=utf-8;');
          echo $this->callback."(".json_encode($data).")";
        }
      }
      else{
        if(empty($this->callback)){
          header("Content-Type: application/json;charset=utf-8;");
          echo json_encode(array("error" => ($this->error)));
        }
        else{
          header('Content-Type: application/json;charset=utf-8;');
          echo json_encode($this->error);
        }
      }
    }
    
    public function required($campos){
      $campos = explode(",", str_replace(" ","",$campos));
      $this->campos = $campos;
      
      foreach($campos as $campo){
        
        if(!in_array($campo, array_keys($this->params))){
          $this->error = "parameters";
          $this->camposFaltantes[] = $campo;
        }
      }
      
      return $this->error == "" ? true : false;
    }

    public function redirect($url){
      $preUrl = "http://";
      if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
          $preUrl = "https://";
        }
      header("Location: ".$preUrl.$this->burl.$url);
    }
    
    public function url($url = null){
      $preUrl = "http://";
      if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
          $preUrl = "https://";
        }
      return $preUrl.$this->burl.$this->murl."/".$url;
    }

    public function revisaSesion(&$sError, $token){
      if(empty($token)){
        $sError .= "No especificaste un token de usuario.";
      } else {
        if($sError == ""):
          $user = new User("WHERE app_token = '$token'");
          if($user->id == ""):
            $sError .= "Tu sesión no es válida ";
          endif;
        endif;
      }
    }

    public function randomColor(){
      return '#' . substr(md5(mt_rand()), 0, 6);
    }

    public function saveBitacora($user, $prospecto, $accion, $estatus){
      $exito = true;
      $model = new Bitacora();
      $model->prospecto = $prospecto;
      $model->accion = $accion;
      $model->usuario = $user;
      $model->estatus = $estatus;

      $pros = new Prospecto($prospecto);
      if($pros->plaza_asignada != ""){
        $model->plaza = $pros->plaza_asignada;
      }
      
      if(!$model->save()){
        $exito = false;
      }

      return $exito;
    }

    public function saveBitacoraPlaza($user, $prospecto, $accion, $estatus, $plaza){
      $exito = true;
      $model = new Bitacora();
      $model->prospecto = $prospecto;
      $model->accion = $accion;
      $model->usuario = $user;
      $model->estatus = $estatus;
      $model->plaza = $plaza;
      
      if(!$model->save()){
        $exito = false;
      }

      return $exito;
    }

    public function revisaPermiso($seccion, $permiso, $rol){
        if($rol == 1) return true;
        $result = false;
        $qry = "SELECT us.permiso FROM user_section us LEFT JOIN section s ON us.section = s.id WHERE us.rol = ".$rol." AND s.name = '".$seccion."' AND s.action = '".$permiso."'";
        $permiso = UserSection::model()->executeQuery($qry);
        $result = ($permiso[0]->permiso == 1) ? true:false;
        return $result;
    }

    public function getMes($mes){
      $aMeses = array(
        1  => "Enero",
        2  => "Febrero",
        3  => "Marzo",
        4  => "Abril",
        5  => "Mayo",
        6  => "Junio",
        7  => "Julio",
        8  => "Agosto",
        9  => "Septiembre",
        10 => "Octubre",
        11 => "Noviembre",
        12 => "Diciembre"
      );

      return $aMeses[$mes];
    }

    public function timeElapsed($seconds = 0){

      $elapsed = "";
    
      if ($seconds < 60) {
          $elapsed = "Ahora";
      } else if ($seconds < 60 * 60 ){
          $minutes = intval($seconds / 60);
          $minText = "min";
          if ($minutes > 1){
              $minText = "mins";
          }
          $elapsed = "Hace $minutes $minText";
      } else if ($seconds < 24 * 60 * 60 ){
          $hours = intval($seconds / (60 * 60));
          $hourText = "hr";
          if ($hours > 1){
             $hourText = "hrs";
          }
          $elapsed = "$hours $hourText";
      } else if ($seconds < 48 * 60 * 60 ){
          $elapsed = "Ayer";
      } else {
          $days = intval($seconds / (24 * 60 * 60));
          $dayText = "d";
          $elapsed = "Hace $days $dayText";
      }
      
      return $elapsed;
    }



    public function setTR($valor1, $valor2){
      $aux = '<tr>';
      $aux .=   '<td class="text-left">'.$valor1.'</td>';
      $aux .=   '<td class="text-right">$ '.number_format($valor2,2).'</td>';
      $aux .= '</tr>';
      return $aux;
    }

    public function sendSMSAWS($destinatario, $mensaje){
      require_once 'lib/aws/aws-autoloader.php';
      $params = array(
        'credentials' => array(
          'key' => Motor::app()->aws_key,
          'secret' => Motor::app()->aws_secret,
        ),
        'region' => 'us-west-2',
        'version' => 'latest'
      );
      $sns = new \Aws\Sns\SnsClient($params);
      $args = array(
        "MessageAttributes" => [
          'AWS.SNS.SMS.SenderID' => [
            'DataType' => 'String',
            'StringValue' => 'Cachito'
          ],
          'AWS.SNS.SMS.SMSType' => [
            'DataType' => 'String',
            'StringValue' => 'Transactional'
          ]
        ],
        "Message" => $mensaje,
        "PhoneNumber" => $destinatario
      );
      try {
        $result = $sns->publish($args);
        return true;
      } catch(Exception $e){
        return false;
      }
    }

    
  }