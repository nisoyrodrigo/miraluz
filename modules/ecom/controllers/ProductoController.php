<?php
class ProductoController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionMicas(){
    $this->render("index-micas");
  }

  public function actionExtras(){
    $this->render("index-extras");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Producto($this->params["id"]);
    $familia = $this->params["familia"];
    if($data->id != ""){
      $familia = $data->familia;
    }
    $this->render("edit", array("data" => $data, "familia"=>$familia));
  }
  
  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    $rows = Producto::model()->findAll("WHERE estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
      $tipo = new TipoProducto($value->tipo);
      $rows[$key]->tipo_nombre = $tipo->nombre;
    }
    $this->renderJSON($rows);
  }

  public function actionGetAllProductos(){
    $template = null;
    $aWhere = "";
    $rows = Producto::model()->findAll("WHERE familia = 'armazon' AND estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
      $tipo = new TipoProducto($value->tipo);
      $marca = new MarcaProducto($value->tipo);
      $rows[$key]->tipo_nombre = $tipo->nombre;
      $rows[$key]->marca_nombre = $marca->nombre;
    }
    $this->renderJSON($rows);
  }

  public function actionGetAllMicas(){
    $template = null;
    $aWhere = "";
    $rows = Producto::model()->findAll("WHERE familia = 'mica' AND estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
      $tipo = new TipoProducto($value->tipo);
      $rows[$key]->tipo_nombre = $tipo->nombre;
    }
    $this->renderJSON($rows);
  }

  public function actionGetAllExtras(){
    $template = null;
    $aWhere = "";
    $rows = Producto::model()->findAll("WHERE familia = 'extra' AND estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
      $tipo = new TipoProducto($value->tipo);
      $rows[$key]->tipo_nombre = $tipo->nombre;
    }
    $this->renderJSON($rows);
  }


  public function actionSave(){
    $model = new Producto($this->params["id"]);
    $portadaPrevio = $model->portada;
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->user = $this->user->id;
    }

    if(!$model->save()){
      $this->error = $model->error;
    } else {
      $dataModel = $model->getAttributes();
      $model = new Producto($dataModel->id);
    } 

    if(!$model->save()){
      $this->error = "Error al actualizar producto".$model->error;
    }

    $adataModel = $model->getAttributes();
    $almacenes = Almacen::model()->findAll();
    foreach ($almacenes as $key => $value) {
      $inventario = new Inventario("WHERE almacen = ".$value->id." AND producto = ".$adataModel->id);
      if($inventario->id == ""){
        $inventario->almacen = $value->id;
        $inventario->producto = $adataModel->id;
        $inventario->cantidad_actual = '0';
        $inventario->save();
      }
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    $model = new Producto($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionReparaInventarios(){
    $this->template = null;
    return;
    $rows = Producto::model()->findAll();
    $almacenes = Almacen::model()->findAll();
    
    foreach ($rows as $key => $value) {
      foreach ($almacenes as $skey => $svalue) {
        $inventario = new Inventario("WHERE almacen = ".$svalue->id." AND producto = ".$value->id);
        if($inventario->id == ""){
          $inventario->almacen = $svalue->id;
          $inventario->producto = $value->id;
          $inventario->cantidad_actual = '0';
          $inventario->save();
        }
      }

    }

    $this->renderJSON($rows);
  }

}