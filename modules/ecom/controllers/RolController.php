<?php
class RolController extends Controller{
  public function actionInit(){
    $users = array();
    foreach(User::model()->findAll() as $row){
      $users[] = array("text" => $row->username, "value" => $row->id);
    }
    $this->render("index", array("users" => $users));
  }
  
  public function actionEditElement(){
    $this->template = null;
    $elementId = $this->params["id"];
    $this->render("edit", array("id" => $elementId));
  }

  public function actionSetPermisos(){
    $this->template = null;
    $elementId = $this->params["id"];
    $this->render("permisos", array("id" => $elementId));
  }

  public function actionGetAll(){
    $rows = Rol::model()->findAll("WHERE id >= 2");
    $this->renderJSON($rows);
  }
  
  public function actionSave(){
    $model = new Rol();
    $model->setAttributes($this->params);
    $model->user = $this->user->id;
    if(!$model->save()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }


  public function actionSavePermisos(){
    $this->template = null;
    $aSalida = array();
    $aSalida["exito"] = false;
    $rolId = $this->params["id"];
    $model = new Rol($rolId);
    if($model->id == "") $this->error = "No existe el rol para asignar permisos.";

    if($this->error == ""):
      foreach ($this->params["seccion"] as $key => $value):
        $data = new UserSection("WHERE section = ".$key." AND rol = ".$model->id." AND user IS NULL");
        $data->permiso = ($value == "") ? 0 : 1;
        $data->rol = $model->id;
        $data->section = $key;
        if(!$data->save()) $this->error = "Error al guardar una permiso.".$data->error;
      endforeach;
    endif;
    if($this->error != ""){
      $model->error .= $this->error;
    }
    $this->renderJSON($model->getAttributes());
  }
  
  
  public function actionDestroy(){
    
    $model = new Rol($this->params["id"]);
    
    $rolUsuario = new User("WHERE rol = ".$model->id);
    
    if(!empty($rolUsuario->id)){
      $this->error = "El rol no se puede eliminar porque hay usuarios ocupandolo, usuario: ".$rolUsuario->producto;
    }
    if($this->error == "" && !$model->remove()){
      $this->error = $model->error;
    }
    
    $this->renderJSON();
  }
}