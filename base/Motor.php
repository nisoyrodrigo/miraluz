<?php
  class Motor{
    
    public $absolute_url;
    public $base_url;
    public $modules;
    public $default_module;
    public $default_controller;
    public $default_action;
    public $auth;
    public $https;
    public $mail_obj;
    
    function __construct(){
      
      require 'config/config.php';
      //require 'lib/Mailer/PHPMailerAutoload.php';

      $this->absolute_url = str_replace("index.php", "", $_SERVER["SCRIPT_FILENAME"]);
      $this->modules = isset($config["modules"]) ? $config["modules"] : array();
      
      foreach($this->modules as $module => $params){
        
        if(isset($params["default"]) && $params["default"] == true){
          $this->default_module = $module;
          $this->auth = isset($params["auth"]) ? $params["auth"] : null;
        }
        if(isset($params["https"])){
          $this->https[$module] = $params["https"];
        }
      }

      $this->default_controller = "Default";
      $this->default_action = "actionInit";
      
    }
    
    public function run(){
      include "base/Controller.php";
      include "base/Model.php";
      include "base/Module.php";
      include "base/View.php";
      include "base/Notification.php";


      
      // Cargamos Controladores
      $this->getDirectory("controllers");
      
      // Cargamos Modelos
      $this->getDirectory("models");

      require 'config/config.php';

      $confCheckUrl = isset($config["checkUrlCMS"]) ? $config["checkUrlCMS"] : null; 
      $model = new stdClass();
    
      if($confCheckUrl != null && $confCheckUrl["active"]){

        $fragUrl = explode("/", $this->absolute_url);
        $principalDirectory = $fragUrl[count($fragUrl) - 2];
        $checkUrl = str_replace("/".$principalDirectory."/", "", $_SERVER["REQUEST_URI"]);

        if($checkUrl[0] == "/"){
          $checkUrl = substr($checkUrl, 1, strlen($checkUrl) - 1);
        }

        if(!empty($checkUrl)){
          $fileModel = $this->absolute_url."modules/".$confCheckUrl["module"].'/models/'.$confCheckUrl["model"].".php";
          require $fileModel;
          if(strpos("?", $checkUrl)>= 0){
            $checkUrl = explode("?", $checkUrl)[0];
          }
          $model = new $confCheckUrl["model"]("WHERE ".$confCheckUrl["field"]." = '".urldecode($checkUrl)."'");
          $id_content_cms = $model->id;
        }
      }

      if(!empty($id_content_cms)){
        $moduleName = $confCheckUrl["module"];
        $controllerName   = $confCheckUrl["controller"];
        $controllerAction = "init";
        $_REQUEST["controller"] = $controllerName;
        $_REQUEST["action"] = $controllerAction; 
      }
      else{

        $moduleName = isset($_REQUEST["module"]) ? (empty($_REQUEST["module"]) ? $this->default_module : $_REQUEST["module"] ) : $this->default_module;
        $controllerName   = isset($_REQUEST["controller"]) ? $_REQUEST["controller"] : $this->default_controller;
        $controllerAction = isset($_REQUEST["action"]) ? "action".ucwords($_REQUEST["action"]) : $this->default_action;
      }
      $put_data = (array)json_decode(trim(file_get_contents('php://input')));
      /////////////////////////////////////////////////////////////////////////
      
      if($moduleName == ""){
        if($controllerName != "" && $controllerName != "php"){

          /* Creamos Data */
          unset($_REQUEST["controller"]);
          unset($_REQUEST["view"]);

          $data = array();
          if(count($_REQUEST) == 0)
            $data = $put_data;
          else if($put_data != null)
            $data = array_merge($_REQUEST, $put_data);
          $classController  = ucwords($controllerName)."Controller";
          //////////////////////////////////////

        
          $class = new $classController($data);
          
          if(method_exists($class, $controllerAction )){
            
            if(count($data) != 0){
              $class->$controllerAction($data);
            }
            else{
              $class->$controllerAction();
            }
          }
          else{
            if($put_data == null){
              echo "afuncion no soportada";
            }
            else{
              header('Content-Type: application/json');
              echo json_encode(array("exito" => false, "error" => "afuncion no soportada"));
            }
          }
        }
        else{
          if($put_data == null){
            try {
              throw new Exception("No se especifico acción a realizar");
            }
            catch(Exception $e){
              echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
          }
          else{
            header('Content-Type: application/json');
            echo json_encode(array("exito" => false, "error" => "No se especifico acción a realizar"));
          }
        }

      }
      else{
        
        if($this->modules[$moduleName]["active"]){
          
          if(in_array($moduleName, array_keys($this->modules))){
            if(!file_exists("modules/".$moduleName) || !file_exists("modules/".$moduleName."/".$moduleName.".module.php")){
              echo "Se intenta llamar al modulo ".$moduleName." pero no existe la carpeta o archivo de configuracion";
            }
            else{
              
              unset($_REQUEST["module"]);
              $_REQUEST["controller"] = $controllerName;
              if(isset($id_content_cms)){
                $_REQUEST["id_content_cms"] = $id_content_cms;
              }
              require_once("modules/".$moduleName."/".$moduleName.".module.php");
            }

          }
          else{
            echo "Se intenta llamar al modulo ".$moduleName." pero no existe la carpeta o archivo de configuracion";
          }
        }
        else{
        
          echo "el modulo ".$moduleName." no se encuentra activo por el momento";
        }
        
      }
      
      
    }
    
    public static function app(){
      include "config/config.php";
      return new Motor;
    }
    
    public static function setUser($name, $user){
      session_start();
      $_SESSION["user".$name] = $user;
    }
    
    public static function getUser($name){
      if(isset($_SESSION) && isset($_SESSION["user".$name]))
      {
        return $_SESSION["user".$name];
      }
      else if(isset($_SESSION) && !isset($_SESSION["user".$name])){
        return null;
      }
      else{
        session_start();
        if(isset($_SESSION["user".$name]))
        {
          return $_SESSION["user".$name];
        }
        else{
          return null;
        }
      }
    }
    
    public static function userLogout($name){
      session_start();
      unset($_SESSION["user".$name]);
    }
    
    function GetDirectory($Directory){

      $MyDirectory = opendir($Directory) or die('Error');
      while($Entry = @readdir($MyDirectory)) {
        if(!is_dir($Directory.'/'.$Entry) && $Entry != '.' && $Entry != '..') {
          require_once($Directory.'/'.$Entry);
        }
      }
      closedir($MyDirectory);
    }
    
    
  }