<?php
class ClienteController extends Controller{

  public function actionInit(){
    $this->render("index");
  }
  
  public function actionEditElement(){
    $this->template = null;
    $data = new Cliente($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionGetAll(){
    $template = null;
    $rows = Cliente::model()->findAll("WHERE estatus = 1");
    foreach ($rows as $key => $value) {
      
    }
    $this->renderJSON($rows);
  }

    public function actionSave(){
      $model = new Cliente($this->params["id"]);
      $portadaPrevio = $model->portada;
      $model->setAttributes($this->params);

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataModel = $model->getAttributes();
        $model = new Cliente($dataModel->id);
      } 
      
      // Si se agregó la imagen para el sabor, la cargamos
      if(isset($_FILES["portada"]) && $this->error == ""){
        $dir_subida = Motor::app()->absolute_url.$this->murl."/images/cliente/";
        if(!file_exists($dir_subida)){
          mkdir($dir_subida, 0775);
        }
        $path = $_FILES['portada']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $fichero_guardado = "images/cliente/".($dataModel->id)."_portada_.".$ext;
        $fichero_subido = $dir_subida."/".($dataModel->id)."_portada_.".$ext;
        if (!move_uploaded_file($_FILES['portada']['tmp_name'], $fichero_subido)) {
          $this->error = "error al guardar foto de portada en carpeta";
          $model->portada = "";
        }
        else{
          $model->portada = $this->murl."/".$fichero_guardado;
        }

      } 


      if(!isset($_FILES["portada"]) && $this->error == ""){
        $model->portada = $portadaPrevio;
      }

      if(!$model->save()){
        $this->error = "Error al actualizar la imagen de portada".$model->error;
      }

      $this->renderJSON($model->getAttributes());
    }

  public function actionFinalizaCliente(){
    $model = new Cliente($this->params["id"]);
    if($model->id == ""){
      $this->error .= "No existe la actividad.";
    }

    if($this->error == ""){
      $model->estatus = $this->params["estatus"];
      if(!$model->save()){
        $this->error .= "Error al actualizar actividad.";
      }
    }

    $this->renderJSON($model);
  }

  public function actionSavePermisos(){
    $aSalida = array();
    $aSalida["exito"] = false;
    $rolId = $this->params["id"];
    $model = new Cliente($rolId);
    if($model->id == "") $this->error = "No existe el rol para asignar permisos.";

    if($this->error == ""):
      foreach ($this->params["seccion"] as $key => $value):
        $data = new UserSection("WHERE section = ".$key." AND rol = ".$model->id." AND user IS NULL");
        $data->permiso = ($value == "") ? 0 : 1;
        $data->rol = $model->id;
        $data->section = $key;
        if(!$data->save()) $this->error = "Error al guardar una preferencia.";
      endforeach;
    endif;
    if($this->error != ""){
      $model->error .= $this->error;
    }
    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    $model = new Cliente($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
}