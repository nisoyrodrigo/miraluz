<?php
class MicaController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Mica($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }
  
  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    $rows = Mica::model()->findAll("WHERE estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
    }
    $this->renderJSON($rows);
  }


  public function actionSave(){
    $model = new Mica($this->params["id"]);
    $portadaPrevio = $model->portada;
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->user = $this->user->id;
    }

    if(!$model->save()){
      $this->error = $model->error;
    } else {
      $dataModel = $model->getAttributes();
      $model = new Mica($dataModel->id);
    } 

    if(!$model->save()){
      $this->error = "Error al actualizar Mica".$model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    $model = new Mica($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }

}