<?php
class ServicioController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionRecoleccion(){
    $data = new Servicio($this->params["id"]);
    $this->render("recoleccion", array("data" => $data));
  }
  
  public function actionEditElement(){
    $this->template = null;
    $data = new Servicio($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }
  
  public function actionEditElementFoto(){
    $this->template = null;
    $servicio = new Servicio($this->params["servicio"]);
    $data = new ServicioFoto($this->params["id"]);
    $this->render("edit-foto", array("data" => $data, "servicio" => $servicio));
  }

  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    if($this->user->rol == "1"){
      $aWhere = " AND 1 = 1";
    }
    if($this->user->rol == "2"){
      $aWhere = " AND user = ".$this->user->id;
    }
    if($this->user->rol == "3"){
      $aWhere = " AND operador = ".$this->user->id;
    }
    $rows = Servicio::model()->findAll("WHERE estatus != 4 ".$aWhere);
    foreach ($rows as $key => $value) {
      $operador = new Operador("WHERE id = ".$value->operador);
      $estatus_descripcion = "";
      $estatus = new ServicioEstatus($value->estatus);
      $rows[$key]->estatus_descripcion = $estatus->nombre;
      $rows[$key]->operador_nombre = $operador->nombre." ".$operador->apaterno;
    }
    $this->renderJSON($rows);
  }

  public function actionGetAllFotos(){
    $template = null;
    $rows = ServicioFoto::model()->findAll("WHERE servicio = ".$this->params["id"]);
    foreach ($rows as $key => $value) {
      $tipo_descripcion = "";
      $tipo = new TipoFoto($value->tipo);
      $rows[$key]->tipo_descripcion = $tipo->nombre;
      $rows[$key]->foto = '<img src="'."https://".$this->burl.$value->portada.'" width="100">';

    }
    $this->renderJSON($rows);
  }

    public function actionSave(){
      $model = new Servicio($this->params["id"]);
      $portadaPrevio = $model->portada;
      $model->setAttributes($this->params);

      if($model->id == ""){
        $model->user = $this->user->id;
      }

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataModel = $model->getAttributes();
        $model = new Servicio($dataModel->id);
      } 
      
      // Si se agregó la imagen para el sabor, la cargamos
      if(isset($_FILES["portada"]) && $this->error == ""){
        $dir_subida = Motor::app()->absolute_url.$this->murl."/images/servicio/";
        if(!file_exists($dir_subida)){
          mkdir($dir_subida, 0775);
        }
        $path = $_FILES['portada']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $fichero_guardado = "images/servicio/".($dataModel->id)."_portada_.".$ext;
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

  public function actionFinalizaServicio(){
    $model = new Servicio($this->params["id"]);
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
    $model = new Servicio($rolId);
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
    $model = new Servicio($this->params["id"]);
    $model->estatus = 4;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionRecolecta(){
    $model = new Servicio($this->params["id"]);
    $model->estatus = 2;
    $model->recoleccion = date('Y-m-d', time());
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionSaveFoto(){
    $model = new ServicioFoto($this->params["id"]);
    $portadaPrevio = $model->portada;
    $model->setAttributes($this->params);

    if(!$model->save()){
      $this->error = $model->error;
    } else {
      $dataModel = $model->getAttributes();
      $model = new ServicioFoto($dataModel->id);
    } 
    
    // Si se agregó la imagen para el sabor, la cargamos
    if(isset($_FILES["portada"]) && $this->error == ""){
      $dir_subida = Motor::app()->absolute_url.$this->murl."/images/serviciofoto/";
      if(!file_exists($dir_subida)){
        mkdir($dir_subida, 0775);
      }
      $path = $_FILES['portada']['name'];
      $ext = pathinfo($path, PATHINFO_EXTENSION);
      $fichero_guardado = "images/serviciofoto/".($dataModel->id)."_portada_.".$ext;
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
}