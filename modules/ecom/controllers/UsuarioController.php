<?php
class UsuarioController extends Controller{
  public function actionInit(){
    $users = array();
    foreach(User::model()->findAll() as $row){
      $users[] = array("text" => $row->username, "value" => $row->id);
    }
    $roles = array();
    foreach(Rol::model()->findAll("WHERE id >= 3") as $row){
      $roles[$row->id] = array("text" => $row->name, "value" => $row->id);
    }
    $this->render("index", array("users" => $users, "roles" => $roles));
  }
  
  public function actionEditElement(){
    $this->template = null;
    $elementId = $this->params["id"];
    $roles = array();
    foreach(Rol::model()->findAll("WHERE id >= 3") as $row){
      $roles[$row->id] = array("text" => $row->name, "value" => $row->id);
    }
    $this->render("edit", array("id" => $elementId, "roles" => $roles));
  }

  public function actionGetAll(){
    $rows = User::model()->findAll("WHERE 1=1");
    $this->renderJSON($rows);
  }
  
  public function actionSave(){
    $model = new User();
    $model->setAttributes($this->params);
    $accionSeguimiento = (empty($model->id)) ? "creado":"editado";
    $model->user = $this->user->id;
    if(!$model->save()){
      $this->error = $model->error;
    }
    if($this->error == ""){
      $seguimiento = new Seguimiento();
      $seguimiento->descripcion = "ha ".$accionSeguimiento." un usuario.";
      $seguimiento->user = $this->user->id;
      if(!$seguimiento->save()){
        $this->error = "Error al guardar seguimiento.";
      }
    }
    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    
    $model = new User($this->params["id"]);
    
    if($model->id == ""):
      $this->error = "No existe el usuario";
    else:
      $model->status = "Eliminate";
    endif;
    if(!$model->save()){
      $this->error = $model->error;
    }
    
    $this->renderJSON();
  }
}