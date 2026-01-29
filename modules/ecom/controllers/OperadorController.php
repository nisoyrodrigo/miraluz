<?php
class OperadorController extends Controller{
  public function actionInit(){
    $this->render("index");
  }
  
  public function actionEditElement(){
    $this->template = null;
    $data = new Operador($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionGetAll(){
    $model = new Operador("WHERE user = ".$this->user->id);
    $sWhere = "WHERE user != 1 AND (";
    if($this->user->id != 1){
      $sWhere .= " 1 = 1 ";
    } else {
      $sWhere .= " 1 = 1 ";
    }
    $sWhere .= ")";
    //$this->error = $sWhere;
    $rows = Operador::model()->findAll($sWhere);
    foreach ($rows as $key => $value) {
      $estatus_descripcion = "Activo";
      $user = new User($value->user);
      $uRol = new Rol($user->rol);
      switch ($user->status) {
        case 'Suspend':
          $estatus_descripcion = "Suspendido";
          break;
        case 'Eliminate':
          $estatus_descripcion = "Eliminado";
          break;
      }
      $rows[$key]->estatus_descripcion = $estatus_descripcion;
      $rows[$key]->rol_descripcion = $uRol->name;
      $rows[$key]->username = $user->username;
    }
    $this->renderJSON($rows);
  }

  
  public function actionSave(){
    $model = new Operador(); 

    $rolPrevio = $model->tipo;
    $this->params["sucursales"] = implode(",", $this->params["sucursales"]);
    $model->setAttributes($this->params);
  
    if($model->user == "" && $this->params["password"] == ""){
      $this->error = "Ingresa un password de acceso al sistema.";
    }

    //$this->error = "tipo:".$this->params["tipo"]." -> Rol: ".$rol;

    if($model->id == ""){
      $model->admin = $this->user->id;
    }


    if($this->error == "" && $model->user == ""){
    
      $user = new User();
      $user->username = $this->params["correo"];
      $user->password = hash('sha256', $this->params["password"]);
      $user->status = "Active";
      $user->rol = $this->params["rol"];
      if(!$user->save()){
        $this->error .= "Error al generar usuario.";
      } else {
        $dataUser = $user->getAttributes();
      }

    }
    if($model->user != "" && $this->params["password"] != ""){
      $aUsuario = new User($model->user);
      $aUsuario->password = hash('sha256', $this->params["password"]);
      $aUsuario->rol = $this->params["rol"];
      $aUsuario->save();
    }

    if($model->user != ""){
      $aUsuario = new User($model->user);
      $aUsuario->rol = $this->params["rol"];
      $aUsuario->save();
    }

    if($this->error == ""){
      $model->user = ($model->user == "") ? $dataUser->id:$model->user;
      if(!$model->save()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    
    $model = new Operador($this->params["id"]);
  
        
    if(!$model->remove()){
      $this->error .= "Error al eliminar operador.";
    } else {
      $user = new User($model->user);
      $user->status = 'Suspend';
      if(!$user->save()){
        $this->error .= "Error al eliminar cuenta de operador.";
      }
    }
    
    $this->renderJSON();
  }


  public function actionSwitchRol(){
    
    $model = new Operador($this->params["id"]);
    $user = new User($model->user);
    $user->rol = ($user->rol == 3) ? 5:3;
    if(!$user->save()){
      $this->error = "Error al modificar reclutador.".$user->error;
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionFiltraMunicipios(){
    $this->template = null;
    $aSalida = array();
    $rec = new Operador($this->params["id"]);
    $aMunicipios = explode(",", $rec->municipios);
    $busqueda = "";
    if($this->params["search"] != ""){
      $palabras = explode(" ", $this->params["search"]);
      $sep = "";
      $busqueda  = " AND ( ";
      foreach ($palabras as $key => $value) {
        $busqueda .= $sep." municipio LIKE '%".$value."%'";
        $sep = " OR ";
      }
      $busqueda .= ")";
    }
    $rows = PlazaMunicipio::model()->findAll("WHERE plaza = ".$this->params["plaza"].$busqueda);
    //$this->error = "WHERE plaza = ".$this->params["plaza"].$busqueda;
    foreach ($rows as $key => $value) {
      $data = array();
      $data["id"] = $value->id;
      $data["text"] = $value->municipio;
      if(in_array($value->id, $aMunicipios)){
        $data["selected"] = "selected";
      }
      $aSalida["results"][] = $data;
    }
    $this->renderJSON($aSalida);
  }

  public function actionSuspender(){
    $this->template = null;
    $model = new Operador($this->params["id"]);

    if($this->error == ""){
      $user = new User($model->user);
      $user->status = 'Suspend';
      $user->app_token = '';
      $user->app_fcm_token = '';
      if(!$user->save()){
        $this->error = "Error al actualizar usuario del operador.";
      }
    }

    if($this->error == ""){
      $model->estatus = "0";
      if(!$model->save()){
        $this->error = "Error al suspender operador.";
      }
    }
    $this->renderJSON();
  }


  public function actionReactivar(){
    $this->template = null;
    $model = new Operador($this->params["id"]);
    if($this->error == ""){
      $user = new User($model->user);
      $user->status = 'Active';
      $user->app_token = '';
      $user->app_fcm_token = '';
      if(!$user->save()){
        $this->error = "Error al actualizar usuario del operador.";
      }
    }

    if($this->error == ""){
      $model->estatus = "0";
      if(!$model->save()){
        $this->error = "Error al reactivar operador.";
      }
    }
    $this->renderJSON();
  }

  /*
   * Get Colonias
   */
  public function actionGetAllColonias(){
    $this->template = null;
    $rows = OperadorColonia::model()->findAll("WHERE reclutador = ".$this->params["reclutador"]);
    foreach ($rows as $key => $value) {
      $estado = new Estado($value->estado);
      $rows[$key]->nombre_estado = $estado->nombre;
    }
    $this->renderJSON($rows);
  }

  public function actionGetMunicipiosPlaza(){
    $this->template = null;
    $reclutador = new Operador("WHERE user = ".$this->user->id);
    $municipiosPrevios = $this->params["municipio"];
    $municipios = PlazaMunicipio::model()->findAll("WHERE plaza IN(".trim(implode(",", $this->params["plazas"])).")");
    $sHtml = '<option value="">Selecciona un municipio...</option>';
    //$this->error = $this->params;
    foreach ($municipios as $key => $value) {
      $checked = (in_array($value->id, $municipiosPrevios)) ? "selected":"";
      $sHtml .= '<option value="'.$value->municipio.'" '.$checked.'>'.$value->municipio.'</option>';
    }
    if(count($this->params["plazas"]) == 0){
       $sHtml = '<option value="">Selecciona un municipio...</option>'; 
    }
    $this->renderJSON($sHtml);
  }

  public function actionGetMunicipios(){
    $this->template = null;
    $reclutador = new Operador("WHERE user = ".$this->user->id);
    $estado = $this->params["estado"];
    $municipios = PlazaMunicipio::model()->findAll("WHERE estado = ".$estado." GROUP BY municipio");
    $sHtml = '<option value="">Selecciona un municipio...</option>';
    foreach ($municipios as $key => $value) {
      $sHtml .= '<option value="'.$value->municipio.'">'.$value->municipio.'</option>';
    }
    $this->renderJSON($sHtml);
  }

  public function actionGetColonias(){
    $this->template = null;
    $estado = $this->params["estado"];
    $municipios = CodigoPostal::model()->findAll("WHERE estado = ".$estado." AND municipio = '".$this->params["municipio"]."' GROUP BY colonia");
    $sHtml = '<option value="">Selecciona una colonia...</option>';
    foreach ($municipios as $key => $value) {
      $sHtml .= '<option value="'.$value->colonia.'">'.$value->colonia.'</option>';
    }
    $this->renderJSON($sHtml);
  }

  public function actionSaveOperadorColonia(){
    $this->template = null;
    $model = new OperadorColonia($this->params["id"]);
    $model->setAttributes($this->params);
    if(!$model->save()){
      $this->error .= "Error al agregar colonia.";
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionEliminaColonia(){
    $this->template = null;
    $model = new OperadorColonia($this->params["id"]);
    if(!$model->remove()){
      $this->error = "No se pudo eliminar la colonia.";
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionReportePlazasN(){
    $this->template = null;
    $rows = Operador::model()->findAll("WHERE estatus = 1 AND plazasn IS NULL");
    foreach ($rows as $key => $value) {
      $nombrePlazas = "";
      $nombrePlazas = Plaza::model()->executeQuery("SELECT GROUP_CONCAT(nombre) AS plazass FROM ec_plaza WHERE id IN(".$value->plazas.")")[0]->plazass;
      $item = new Operador($value->id);
      $item->plazasn = $nombrePlazas;
      $item->save();
    }
    $this->renderJSON($rows);
  }

  public function actionSendClaves(){
    $this->template = null;
    return;
    require('lib/Mailer/PHPMailerAutoload.php');
    $rows = Operador::model()->findAll("WHERE id >= 25");
    $enviados = 0;

    foreach ($rows as $key => $value) {
      $user = new User($value->user);
      $sHtml = '';

      $sHtml = file_get_contents("templates/mailing/acceso.tpl.php");
      $titulo  = "¡Hola ".$value->nombre."! <br> Aquí tienes tus datos de acceso";
      $mensaje = "Usuario: ".$user->username." <br>";
      $mensaje .= "Contraseña: ".$user->contra."o123 <br>";
      $sHtml = str_replace("##titulo##", $titulo, $sHtml);
      $sHtml = str_replace("##mensaje##", $mensaje, $sHtml);
      $sHtml = str_replace("##nombre##", $value->nombre, $sHtml);
      $sHtml = str_replace("##tituloBoton##", "https://oxxo.cuarto101.mx/v2/", $sHtml);
      $sHtml = str_replace("##botonUrl##", "https://oxxo.cuarto101.mx/v2/", $sHtml);

      $mail = new PHPMailer;
      $mail->CharSet = 'UTF-8';
      $mail->isSMTP();
      $mail->Host = 'email-smtp.us-east-1.amazonaws.com';
      $mail->Username = 'AKIAINUBGIFNP325UFHA';
      $mail->Password = 'Aumq4i/jLhYgljc5zqnyvCylbiwtxOKGhGh7yDPfvYxe';
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->setFrom('contacto@cuarto101.mx', 'Cuarto 101');
      $mail->Subject = "Datos de acceso Plataforma V2.";
      $mail->addAddress($user->username, $value->nombre);
      //$mail->addAddress("yrodrigocorn@icloud.com", $value->nombre);
      $mail->isHTML(true);
      $mail->Body = $sHtml;

      if(!$mail->send()) {
        $this->error = "Error al mandar mail";
      } else {
        $enviados = $enviados + 1;
      }

    }

    $this->error = "Enviados:".$enviados;
    $this->renderJSON();
  }
}