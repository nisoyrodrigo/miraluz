<?php
class CompraController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Compra($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionAgregarProducto(){
    $this->template = null;
    $data = new Compra($this->params["compra"]);
    $this->render("agregar-producto", array("data" => $data, "tipo"=>$this->params["tipo"]));
  }

  public function actionEditar(){
    $data = new Compra($this->params["id"]);
    $this->render("editar", array("data" => $data));
  }
  
  public function actionGetAll(){
    $this->template = null;

    $aSalida = array();

    $columns = array( 
      0 =>'c.id',
      1 => 'c.user',
      2 => 'c.proveedor',
      3 => 'a.nombre',
      4 => 'c.total',
      5 => 'c.referencia',
      6 => 'c.created',
      7 => 'c.estatus',
    );

    $aWhere = "";
    if (!empty($this->params['search']['value'])) {
      $search = $this->params['search']['value'];
      $aWhere .= " AND (c.id LIKE '%$search%' OR a.nombre LIKE '%$search%' OR c.proveedor LIKE '%$search%' OR c.referencia LIKE '%$search%') ";
    }

    $rows = Compra::model()->findAll("WHERE 1 = 1 ".$aWhere);

    $count = Compra::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_compra c LEFT JOIN ec_almacen a ON c.almacen = a.id WHERE 1 = 1 ".$aWhere);
    $rows = Compra::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_compra c LEFT JOIN ec_almacen a ON c.almacen = a.id WHERE 1 = 1 ".$aWhere);

    $rowsquery = Venta::model()->executeQuery("
        SELECT 
            c.*, 
            a.nombre AS almacen_nombre
        FROM ec_compra c
        LEFT JOIN ec_almacen a ON c.almacen = a.id
        WHERE 1 = 1
        $aWhere
        ORDER BY ". $columns[$this->params['order'][0]['column']] ." 
          ".$this->params['order'][0]['dir']."
        LIMIT ".$this->params["start"].", ".$this->params["length"]."
    ");

    foreach ($rowsquery as $key => $value) {
      $operador = new Operador("WHERE user = ".$value->user);
      $rowsquery[$key]->operador = $operador->nombre." ".$operador->apaterno." ".$operador->amaterno;
    }
      
    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($rows[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);

  }

  public function actionGetAllDetalle(){
    $this->template = null;
    $rows = CompraDetalle::model()->findAll("WHERE compra = ".$this->params["id"]);
    foreach ($rows as $key => $value) {
      $producto = new Producto($value->producto);
      $rows[$key]->producto_nombre = $producto->nombre;
    }
    $this->renderJSON($rows);
  }


  public function actionSave(){
    $model = new Compra($this->params["id"]);
    $portadaPrevio = $model->portada;
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->user = $this->user->id;
    }

    if(!$model->save()){
      $this->error = $model->error;
    } else {
      $dataModel = $model->getAttributes();
      $model = new Compra($dataModel->id);
    } 

    if(!$model->save()){
      $this->error = "Error al actualizar Compra".$model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    $model = new Compra($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroyCompraProducto(){
    $this->template = null;
    $model = new CompraDetalle($this->params["id"]);
    $compra = new Compra($model->compra);
    if($compra->estatus == "completada"){
      $this->error .= "No puedes eliminar un producto, la compra ya está cerrada.";
    }

    if($this->error == ""){
      if(!$model->remove()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON();
  }



  public function actionAgregaProducto(){
    $this->template = null;
    $model = new CompraDetalle("WHERE compra = ".$this->params["compra"]." AND producto = ".$this->params["id"]);
    if($model->id != ""){
      $model->cantidad = $this->params["cantidad"];
    } else {
      $model->compra = $this->params["compra"];
      $model->producto = $this->params["id"];
      $model->cantidad = $this->params["cantidad"];
    }

    if(!$model->save()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionTerminaCompra(){
    $this->template = null;
    $model = new Compra($this->params["id"]);
    $almacen = new Almacen($model->almacen);
    $detalle = CompraDetalle::model()->findAll("WHERE compra = ".$model->id);
    if($model->estatus != "pendiente"){
      $this->error .= "La compra ya fue cerrada.";
    }
    if($this->error == ""){
      foreach ($detalle as $key => $value) {
        $almacenMovimiento = new AlmacenMovimiento();
        $almacenMovimiento->almacen = $almacen->id;
        $almacenMovimiento->producto = $value->producto;
        $almacenMovimiento->tipo = "entrada";
        $almacenMovimiento->cantidad = $value->cantidad;
        $almacenMovimiento->referencia = $value->id;
        $almacenMovimiento->save();

        $inventario = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        if($inventario->id != ""){
          $inventario->cantidad_actual = $inventario->cantidad_actual + $value->cantidad;
          $inventario->save();
        }
      }
    }

    if($this->error == ""){
      $model->estatus = "completada";
      if(!$model->save()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON();
  }

}