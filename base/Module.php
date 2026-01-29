<?php 
  class Module{

    public $default_controller;
    public $default_action;
    public $burl;
    public $murl;
    public $module;
    public $auth;
    public $registerSections = false;
    public $error;
    public $user;
    public $template;

    public function __construct(){
      
      require 'config/config.php';
      $this->template;
      $this->default_controller = "Default";
      $this->default_action = "actionInit";
      $this->burl = $_SERVER["HTTP_HOST"].str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]); 
      $this->murl = str_replace("Module", "", "modules/".get_called_class());
      $this->module = str_replace("Module", "", get_called_class());
      
      if(isset($config["modules"][$this->module]["template"])){
        $this->template = Motor::app()->absolute_url.$this->murl."/templates/".$config["modules"][$this->module]["template"].".tpl.php";
        if(!file_exists($this->template)){
          echo "el template ".$this->template." para ".$this->module." no existe ";
        }
      }

      if(isset($config["modules"][$this->module]["registerSections"])){
        $this->registerSections = $config["modules"][$this->module]["registerSections"];
      }

      $this->auth = isset($config["modules"][$this->module]["auth"]) ? $config["modules"][$this->module]["auth"] : null;
    }

    public function run(){
      //Cargamos Controladores
      
      $this->getDirectory($this->murl."/controllers");
      
      // Cargamos Modelos
      $this->getDirectory($this->murl."/models");
      
      /* Creamos Data */
      $controllerName   = isset($_REQUEST["controller"]) ? $this->getCleanUrl($_REQUEST["controller"]) : $this->default_controller;
      $controllerAction = isset($_REQUEST["action"]) ? "action".$this->getCleanUrl($_REQUEST["action"]) : $this->default_action;
      $put_data = (array)json_decode(trim(file_get_contents('php://input')));
      ////////////////////////////////////////////////////////////////////////////////

      $data = array();

      $classController  = $controllerName."Controller";
      $data["burl"] = $this->burl;
      $data["murl"] = $this->murl;
      $data["module"] = $this->module;
      
      unset($_REQUEST["controller"]);
      unset($_REQUEST["view"]);

      if(count($_REQUEST) !=0 ){
        $data = array_merge($_REQUEST, $data);
      }
      if(count($put_data)){
        $data = array_merge($put_data, $data);
      }

      /* Para el log */
      $logData = $data;
      unset($logData["action"]);
      unset($logData["burl"]);
      unset($logData["murl"]);
      
      $this->user = Motor::app()->getUser($this->module);
      

      //$this->registerLog($this->module, $controllerName, str_replace("action", "",$controllerAction), $logData);
                  
      if(!empty($this->auth) && empty($this->user)){
        $classController  = ucwords($this->auth["controllerAuth"]);
        if(class_exists($classController)){
          $class = new $classController($data);
          $action = "action".ucwords($this->auth["loginAction"]);
          $class->$action();
        }
        else{
          if($put_data == null){
            echo "No existe el archivo ".$classController."  en el modulo ".$this->module;
          }
          else{
            header('Content-Type: application/json');
            echo json_encode(array("exito" => false, "error" => "No existe el archivo ".$classController."  en el modulo ".$this->module));
          }
        }
      }
      else{
        
        if($this->registerSections && $controllerAction != "action".ucwords($this->auth["loginAction"]) && $controllerAction != "action".ucwords($this->auth["logoutAction"])){
          $this->checkUserPermissions($this->registerSection($controllerName, str_replace("action", "",$controllerAction)));
        }

        //////////////////////////////////////
        $class = new $classController($data);
        
        if(method_exists($class, $controllerAction )){  
          $class->$controllerAction();
        }
        else{
          if($put_data == null){
            echo "funcion no soportada";
          }
          else{
            header('Content-Type: application/json');
            echo json_encode(array("exito" => false, "error" => "funcion no soportada"));
          }
        }
      }
    }

    function GetDirectory($Directory){
      $MyDirectory = opendir($Directory) or die('Error no existe el directorio: '.$Directory);
      while($Entry = @readdir($MyDirectory)) {
        if(!is_dir($Directory.'/'.$Entry) && $Entry != '.' && $Entry != '..') {
          if(!class_exists(str_replace(".php", "", $Entry))){
            require_once($Directory.'/'.$Entry);
          }
        }
      }
      closedir($MyDirectory);
    }

    function getCleanUrl($data){
      $data = str_replace("-", "", $data);
      $data = str_replace("/", "", $data);
      $data = ucwords($data);
      return $data;
    }

    function registerSection($controller, $action){
      $section = new Section("WHERE name = '".$controller."' AND (action = '".$action."')");
      
      if(empty($section->id)){
        $section->name = $controller;
        $section->action = $action;
        if($section->status != "Active"){
          die("El modulo no esta activo");
        }
        if(!$section->save()){
          die("Error al intentar crear la seccion: ".$section->error);
        }
      }

      return $section->id;
    }

    function registerLog($module, $controller, $action, $data){

      $usuario = "nousuario";
      if($module == "web"){
        $token = $data['token'];
        if($token != ""){
          $us = new User("WHERE app_token = '$token'");
          if($us->id != ""){
            $usuario = $us->id;
          }
        }
      }

      if($this->user->id != ""){
        $usuario = $this->user->id;
      }

      if($usuario != "nousuario"){
        $lmodel = new Log();
        $lmodel->user = $usuario;
        $lmodel->modulo = $module;
        $lmodel->controlador = $controller;
        $lmodel->accion = $action;
        $lmodel->data = json_encode($data);
        if(!$lmodel->save()){
          
        }
      }
      
    }

    function checkUserPermissions($section){
      $user_section = new UserSection("WHERE (user = ".$this->user->id." OR (rol = ".$this->user->rol." )) AND section = ".$section);
      if(empty($user_section->id) && $this->user->id == 1){
        $user_section->user = $this->user->id;
        $user_section->section = $section;
        $user_section->permiso = 1;
        $user_section->rol = 1;
        $user_section->save();
      }
      if($user_section->permiso != 1){
        die("No tienes permiso para ver o realizar esta accion");
      }
    }

  }
?>