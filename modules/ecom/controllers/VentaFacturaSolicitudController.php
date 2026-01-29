<?php

class VentaFacturaSolicitudController extends Controller{

  public function actionInit(){
    $this->render("index");
  }



  public function actionAgregar(){
    $this->render("agregar");
  }

  public function actionEditar(){
    $data = new VentaFacturaSolicitud($this->params["data"]);
    $this->render("editar", array("data"=>$data));
  }


  public function actionDemo(){
    $this->render("demo");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $venta = new Venta($data->venta);
    $this->render("edit", array("data" => $data, "venta"=>$venta));
  }

  public function actionDetalle(){
    $this->template = null;
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $this->render("detalle", array("data" => $data));
  }

  public function actionAgregaCliente(){
    $this->template = null;
    $this->render("agrega-cliente");
  }

  public function actionAgregarProducto(){
    $this->template = null;
    $this->render("agrega-producto", array("tipo" => $this->params["tipo"]));
  }

  public function actionAgregarProductoEdit(){
    $this->template = null;
    $this->render("agrega-producto-edit", array("tipo" => $this->params["tipo"]));
  }

  public function actionEditAbono(){
    $this->template = null;
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $this->render("edit-abono", array("data"=>$data));
  }

  public function actionEditEstatus(){
    $this->template = null;
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $this->render("edit-estatus", array("data"=>$data));
  }

  public function actionConfirmaVendedor(){
    $this->template = null;
    $this->render("confirma-vendedor", array("sucursal" => $this->params["sucursal"], "vendedor" => $this->params["vendedor"]));
  }
  
  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    $rows = VentaFacturaSolicitud::model()->findAll("WHERE 1 = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
      $venta = new Venta($value->venta);
      $sucursal = new Sucursal($venta->sucursal);
      $cliente = new Cliente($venta->cliente);

      $rows[$key]->venta_folio = $venta->folio;
      $rows[$key]->sucursal_nombre = $sucursal->nombre;
    }
    $this->renderJSON($rows);
  }


  public function actionGetAllCotizaciones(){
    $template = null;
    $aWhere = "";
    $operador = new Operador("WHERE user = ".$this->user->id);
    $rows = VentaFacturaSolicitud::model()->findAll("WHERE tipo = 'cotizacion' AND sucursal IN(".$operador->sucursales.") ".$aWhere);
    foreach ($rows as $key => $value) {
      $cliente = new Cliente($value->cliente);
      $productos = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = ".$value->id);
      $sep = "";
      $aDetalle = "";
      foreach ($productos as $skey => $svalue) {
        $sproduct = new Producto($svalue->producto);
        $aDetalle .= $sep.$sproduct->nombre;
        $sep = "<br>";
      }

      $vendedor = new Operador("WHERE user = ".$value->user);
      $rows[$key]->vendedor_nombre = $vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;

      $rows[$key]->nombre_cliente = $cliente->nombre;
      $rows[$key]->telefono_cliente = $cliente->telefono;
      $rows[$key]->productos_descripcion = $aDetalle;

      $createdTimestamp = strtotime($value->created);
      $now = time();
      $diasDiferencia = ($now - $createdTimestamp) / (60 * 60 * 24);
      $rows[$key]->permite_venta = ($diasDiferencia <= 90) ? 1 : 0;
    }
    $this->renderJSON($rows);
  }


  public function actionGetAllVentaFacturaSolicituds(){
    $template = null;


    $aSalida = array();

    $columns = array( 
      0 =>'v.id',
      1 => 'c.nombre',
      2 => 'c.telefono',
      3 => 'productos_descripcion',
      4 => 'v.subtotal',
      5 => 'v.descuento',
      6 => 'v.total',
      7 => 'v.anticipo',
      8 => 'abonos',
      9 => 'v.saldo',
      10 => 'v.fecha_venta',
      12 => 'v.estatus'
    );


    $aWhere = "";
    if (!empty($this->params['search']['value'])) {
      $search = $this->params['search']['value'];
      $aWhere .= " AND (v.id LIKE '%$search%' OR c.nombre LIKE '%$search%' OR c.telefono LIKE '%$search%' OR v.folio LIKE '%$search%') ";
    }
    $operador = new Operador("WHERE user = ".$this->user->id);
    $rows = VentaFacturaSolicitud::model()->findAll("WHERE tipo = 'venta' AND sucursal IN(".$operador->sucursales.") ".$aWhere);

    $count = VentaFacturaSolicitud::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    $rows = VentaFacturaSolicitud::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    //$rowsquery = VentaFacturaSolicitud::model()->executeQuery("SELECT v.*, c.nombre, c.telefono, GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos_descripcion FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $aWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"]);
      
    $rowsquery = VentaFacturaSolicitud::model()->executeQuery("
        SELECT 
            v.*, 
            c.nombre AS nombre_cliente, 
            c.telefono AS telefono_cliente, 
            GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos_descripcion,
            (
                SELECT IFNULL(SUM(vm.monto), 0) 
                FROM ec_venta_movimiento vm 
                WHERE vm.venta = v.id AND vm.tipo = 'ingreso' AND vm.numero > 1
            ) AS abonos
        FROM ec_venta v
        LEFT JOIN ec_cliente c ON v.cliente = c.id
        LEFT JOIN ec_venta_detalle vd ON vd.venta = v.id
        LEFT JOIN ec_producto p ON vd.producto = p.id
        WHERE v.tipo = 'venta' 
          AND v.sucursal IN(".$operador->sucursales.") 
        $aWhere
        GROUP BY v.id
        ORDER BY ". $columns[$this->params['order'][0]['column']] ." 
          ".$this->params['order'][0]['dir']."
        LIMIT ".$this->params["start"].", ".$this->params["length"]."
    ");
    // $this->error = "SELECT v.*, c.nombre, c.telefono FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"];

    foreach ($rowsquery as $key => $value) {
      $cliente = new Cliente($value->cliente);
      $productos = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = ".$value->id);
      $sep = "";
      $aDetalle = "";
      foreach ($productos as $skey => $svalue) {
        $sproduct = new Producto($svalue->producto);
        $aDetalle .= $sep.$sproduct->nombre;
        $sep = "<br>";
      }

      $vendedor = new Operador("WHERE user = ".$value->user);
      $rowsquery[$key]->vendedor_nombre = $vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;
      $rowsquery[$key]->nombre_cliente = $cliente->nombre;
      $rowsquery[$key]->telefono_cliente = $cliente->telefono;
      //$rowsquery[$key]->productos_descripcion = $aDetalle;

      $abonos = VentaFacturaSolicitudMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$value->id)[0]->total;
      // $rowsquery[$key]->abonos = $abonos ?? 0;
      $rowsquery[$key]->saldo = $value->saldo - $abonos;
      $estatus = new VentaFacturaSolicitudEstatus($value->estatus);
      $rowsquery[$key]->estatus_descripcion = $estatus->nombre;
    }

    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($rows[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);
  }

  public function actionGetAllTemporales(){
    $this->template = null;
    $rows = ProductoTemporal::model()->findAll("WHERE user = ".$this->user->id);
    foreach ($rows as $key => $value) {
      $producto = new Producto($value->producto);
      $rows[$key]->nombre = $producto->nombre;
      $rows[$key]->familia = $producto->familia;
      $rows[$key]->precio_publico = $producto->precio_publico;
      $rows[$key]->total = $producto->precio_publico * $value->cantidad;
    }
    $this->renderJSON($rows);
  }

  public function actionGetAllTemporalesEdit(){
    $this->template = null;
    $rows = ProductoTemporalEdit::model()->findAll("WHERE user = ".$this->user->id);
    foreach ($rows as $key => $value) {
      $producto = new Producto($value->producto);
      $rows[$key]->nombre = $producto->nombre;
      $rows[$key]->familia = $producto->familia;
      $rows[$key]->precio_publico = $producto->precio_publico;
      $rows[$key]->total = $producto->precio_publico * $value->cantidad;
    }
    $this->renderJSON($rows);
  }


  public function actionSave(){
    $model = new Cotizacion($this->params["id"]);
    $portadaPrevio = $model->portada;
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->user = $this->user->id;
    }

    if(!$model->save()){
      $this->error = $model->error;
    } else {
      $dataModel = $model->getAttributes();
      $model = new Cotizacion($dataModel->id);
    } 

    if(!$model->save()){
      $this->error = "Error al actualizar Cotizacion".$model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroy(){
    $model = new Cotizacion($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionEliminaAbono(){
    $this->template = null;
    $model = new VentaFacturaSolicitudMovimiento($this->params["id"]);
    if(!$model->remove()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroyTemporal(){
    $model = new ProductoTemporal($this->params["id"]);
    if(!$model->remove()){
      // $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }
  
  public function actionDestroyTemporalEdit(){
    $model = new ProductoTemporalEdit($this->params["id"]);
    if(!$model->remove()){
      // $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionBuscarCliente(){
    $this->template = null;
    $this->render("lista-clientes", array("query" => $this->params["query"])); 
  }

  public function actionBuscarClienteAlt(){
    $this->template = null;
    $this->render("lista-clientes-alt", array("nombre" => $this->params["nombre"], "telefono" => $this->params["telefono"], "correo" => $this->params["correo"])); 
  }

  public function actionAgregaProducto(){
    $this->template = null;
    $model = new ProductoTemporal("WHERE producto = ".$this->params["id"]." AND user = ".$this->user->id);
    if($model->id != ""){
      $model->cantidad = $model->cantidad + 1;
    } else {
      $model->producto = $this->params["id"];
      $model->user = $this->user->id;
      $model->cantidad = 1;
    }

    if(!$model->save()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionAgregaProductoEdit(){
    $this->template = null;
    $model = new ProductoTemporalEdit("WHERE producto = ".$this->params["id"]." AND user = ".$this->user->id);
    if($model->id != ""){
      $model->cantidad = $model->cantidad + 1;
    } else {
      $model->producto = $this->params["id"];
      $model->user = $this->user->id;
      $model->cantidad = 1;
    }

    if(!$model->save()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionSaveVentaFacturaSolicitud(){
    $this->template = null;
    $model = new VentaFacturaSolicitud();
    $model->setAttributes($this->params);

    if($this->params["vendedor_id"] != ""){
      $model->user = $this->params["vendedor_id"];
    } else {
      $model->user = $this->user->id;
    }

    // Definir el prefijo según el tipo
    $prefijo = ($model->tipo === "cotizacion") ? "c" : "v";
    // Obtener la clave de la sucursal
    $sucursal = new Sucursal($model->sucursal);
    $almacen = new Almacen("WHERE sucursal = ".$sucursal->id);
    $claveSucursal = $sucursal->clave;
    // Obtener el año y mes actuales en formato yyyymm
    $fechaActual = date("Ym");
    // Obtener el consecutivo para el tipo y la sucursal
    $consecutivo = VentaFacturaSolicitud::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE tipo = '$model->tipo' AND sucursal = $model->sucursal AND DATE_FORMAT(created, '%Y%m') = '$fechaActual'")[0]->total + 1;
    // Construir el folio
    $model->folio = $prefijo . $claveSucursal . $fechaActual . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);


    $rows = ProductoTemporal::model()->findAll("WHERE user = ".$this->user->id);
    if(count($rows) == 0){
      $this->error .= "Debes seleccionar por lo menos un producto.";
    }

    if($model->tipo == "v" || $model->tipo == "venta"){
      if($model->anticipo > $model->total){
        $this->error .= "No puedes abonar más del saldo pendiente.";
      }

      if($model->anticipo < 1){
        $this->error .= "Para realizar una venta es necesario un anticipo de por lo menos el 10%.";
      }

      if ($model->anticipo < ($model->total * 0.10)) {
        $this->error .= "El anticipo debe ser al menos el 10% del total.";
      }
    }

    if($this->error == ""){
      if($model->tipo == "cotizacion"){
        $model->total = $model->subtotal;
        $model->descuento = 0;
        $model->abono = 0;
      }
      $model->fecha_venta = date('Y-m-d H:i:s', time());
      if($model->tipo_vision == "vision_sencilla"){
        $model->od_add = 0;
        $model->oi_add = 0;
        $model->oi_altura = 0;
        $model->od_altura = 0;
      }

      $model->estatus = 1;
      
      if ($model->tipo == "v") {
        if ($model->anticipo < ($model->total * 0.30)) {
          $model->estatus = 7; // Apartado
        } else {
          $model->estatus = 2; // Laboratorio
        }
      }

      if($this->params["cliente"] == "1"){
        $model->estatus = 4;
      }

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataVentaFacturaSolicitud = $model->getAttributes();
      }
    }

    if($this->error == ""){
      $data = $model->getAttributes();
      foreach ($rows as $key => $value) {
        $producto = new Producto($value->producto);
        $rows[$key]->nombre = $producto->nombre;
        $rows[$key]->familia = $producto->familia;
        $rows[$key]->precio_publico = $producto->precio_publico;
        $rows[$key]->total = $producto->precio_publico * $value->cantidad;

        $dmodel = new VentaFacturaSolicitudDetalle();
        $dmodel->venta = $data->id;
        $dmodel->producto = $producto->id;
        $dmodel->cantidad = $value->cantidad;
        $dmodel->precio = $producto->precio_publico;
        $dmodel->save();
        if($producto->maneja_inventario == "1" && $model->tipo == "venta"){
          $dmodelData = $dmodel->getAttributes();
          $almacenMovimiento = new AlmacenMovimiento();
          $almacenMovimiento->almacen = $almacen->id;
          $almacenMovimiento->producto = $producto->id;
          $almacenMovimiento->tipo = "salida";
          $almacenMovimiento->cantidad = $value->cantidad;
          $almacenMovimiento->referencia = $data->id;
          $almacenMovimiento->save();

          if($model->tipo == "venta"){
            $inventario = new Inventario("WHERE producto = ".$producto->id." AND almacen = ".$almacen->id);
            if($inventario->id != ""){
              $inventario->cantidad_actual = $inventario->cantidad_actual - $value->cantidad;
              $inventario->save();
            }
          }
        }
      }
    }

    if($this->error == "" && $model->tipo == "venta"){
      $movimiento = new VentaFacturaSolicitudMovimiento();
      $movimiento->venta = $dataVentaFacturaSolicitud->id;
      $movimiento->user = $this->user->id;
      $movimiento->tipo = 'ingreso';
      $movimiento->monto = $model->anticipo;
      $movimiento->forma_pago = $model->forma_pago;
      $movimiento->save();
    }

    if($this->error == ""){
      $aQuery = ProductoTemporal::model()->executeQuery("DELETE FROM ec_producto_temporal WHERE user = ".$this->user->id);
    }

    $this->renderJSON($model->getAttributes());
  }



  public function actionSaveVentaFacturaSolicitudEdit(){
    $this->template = null;
    $model = new VentaFacturaSolicitud($this->params["venta_id"]);
    $folio = $model->folio;
    $sucursal = $model->sucursal;
    $model->setAttributes($this->params);
    $model->sucursal = $sucursal;

    if($this->params["vendedor_id"] != ""){
      $model->user = $this->params["vendedor_id"];
    } else {
      $model->user = $this->user->id;
    }

    // Definir el prefijo según el tipo
    $prefijo = ($model->tipo === "cotizacion") ? "c" : "v";
    // Obtener la clave de la sucursal
    $sucursal = new Sucursal($model->sucursal);
    $almacen = new Almacen("WHERE sucursal = ".$sucursal->id);
    // Construir el folio
    $model->folio = $folio;


    $rows = ProductoTemporalEdit::model()->findAll("WHERE user = ".$this->user->id);
    if(count($rows) == 0){
      $this->error .= "Debes seleccionar por lo menos un producto.";
    }

    if($this->error == ""){
      if($model->tipo_vision == "vision_sencilla"){
        $model->od_add = 0;
        $model->oi_add = 0;
        $model->oi_altura = 0;
        $model->od_altura = 0;
      }

      if($this->params["cliente"] == "1"){
        $model->estatus = 4;
      }

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataVentaFacturaSolicitud = $model->getAttributes();
      }
    }

    if($this->error == ""){
      $data = $model->getAttributes();
      $eliminaMovimientosAlmacen = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE referencia = '".$data->id."'");
      $detallePrevio = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = ".$data->id);
      foreach ($detallePrevio as $key => $value) {
        $inv = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        $inv->cantidad_actual = $inv->cantidad_actual + $value->cantidad;
        $inv->save();
      }
      $eliminaDetalle = VentaFacturaSolicitudDetalle::model()->executeQuery("DELETE FROM ec_venta_detalle WHERE venta = '".$data->id."'");
      foreach ($rows as $key => $value) {
        $producto = new Producto($value->producto);
        $rows[$key]->nombre = $producto->nombre;
        $rows[$key]->familia = $producto->familia;
        $rows[$key]->precio_publico = $producto->precio_publico;
        $rows[$key]->total = $producto->precio_publico * $value->cantidad;

        $dmodel = new VentaFacturaSolicitudDetalle();
        $dmodel->venta = $data->id;
        $dmodel->producto = $producto->id;
        $dmodel->cantidad = $value->cantidad;
        $dmodel->precio = $producto->precio_publico;
        $dmodel->save();
        if($producto->maneja_inventario == "1"){
          $dmodelData = $dmodel->getAttributes();
          $almacenMovimiento = new AlmacenMovimiento();
          $almacenMovimiento->almacen = $almacen->id;
          $almacenMovimiento->producto = $producto->id;
          $almacenMovimiento->tipo = "salida";
          $almacenMovimiento->cantidad = $value->cantidad;
          $almacenMovimiento->referencia = $data->id;
          $almacenMovimiento->save();

          $inventario = new Inventario("WHERE producto = ".$producto->id." AND almacen = ".$almacen->id);
          if($inventario->id != ""){
            $inventario->cantidad_actual = $inventario->cantidad_actual - $value->cantidad;
            $inventario->save();
          }
          

        }
      }
    }

    if($this->error == ""){
      $movimiento = new VentaFacturaSolicitudMovimiento("WHERE venta = ".$model->id." AND tipo = 'ingreso' AND numero = '1'");
      $movimiento->venta = $dataVentaFacturaSolicitud->id;
      $movimiento->user = $this->user->id;
      $movimiento->tipo = 'ingreso';
      $movimiento->monto = $model->anticipo;
      $movimiento->forma_pago = $model->forma_pago;
      $movimiento->save();
    }

    if($this->error == ""){
      $aQuery = ProductoTemporalEdit::model()->executeQuery("DELETE FROM ec_producto_temporal_edit WHERE user = ".$this->user->id);
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionSaveCotizacionEdit(){
    $this->template = null;
    $model = new VentaFacturaSolicitud($this->params["venta_id"]);
    $folio = $model->folio;
    $sucursal = $model->sucursal;
    $model->setAttributes($this->params);
    $model->sucursal = $sucursal;

    if($this->params["vendedor_id"] != ""){
      $model->user = $this->params["vendedor_id"];
    } else {
      $model->user = $this->user->id;
    }

    // Definir el prefijo según el tipo
    $prefijo = ($model->tipo === "cotizacion") ? "c" : "v";
    // Obtener la clave de la sucursal
    $sucursal = new Sucursal($model->sucursal);
    $claveSucursal = $sucursal->clave;
    $almacen = new Almacen("WHERE sucursal = ".$sucursal->id);
    // Construir el folio
    $model->folio = $folio;
    $model->fecha_venta = date('Y-m-d H:i:s', time());


    $rows = ProductoTemporalEdit::model()->findAll("WHERE user = ".$this->user->id);
    if(count($rows) == 0){
      $this->error .= "Debes seleccionar por lo menos un producto.";
    }

    if($this->error == ""){

      $fechaActual = date("Ym");
      // Obtener el consecutivo para el tipo y la sucursal
      $consecutivo = VentaFacturaSolicitud::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE tipo = 'venta' AND sucursal = $model->sucursal AND DATE_FORMAT(created, '%Y%m') = '$fechaActual'")[0]->total + 1;
      // Construir el folio
      $model->folio = "v" . $claveSucursal . $fechaActual . str_pad($consecutivo, 4, '0', STR_PAD_LEFT);


      if($model->tipo_vision == "vision_sencilla"){
        $model->od_add = 0;
        $model->oi_add = 0;
        $model->oi_altura = 0;
        $model->od_altura = 0;
      }

      $model->tipo = "venta";
      $model->estatus = 1;

      if($this->params["cliente"] == "1"){
        $model->estatus = 4;
      }

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataVentaFacturaSolicitud = $model->getAttributes();
      }
    }

    if($this->error == ""){
      $data = $model->getAttributes();
      $eliminaMovimientosAlmacen = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE referencia = '".$data->id."'");
      $detallePrevio = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = ".$data->id);
      foreach ($detallePrevio as $key => $value) {
        $inv = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        $inv->cantidad_actual = $inv->cantidad_actual + $value->cantidad;
        $inv->save();
      }
      $eliminaDetalle = VentaFacturaSolicitudDetalle::model()->executeQuery("DELETE FROM ec_venta_detalle WHERE venta = '".$data->id."'");
      foreach ($rows as $key => $value) {
        $producto = new Producto($value->producto);
        $rows[$key]->nombre = $producto->nombre;
        $rows[$key]->familia = $producto->familia;
        $rows[$key]->precio_publico = $producto->precio_publico;
        $rows[$key]->total = $producto->precio_publico * $value->cantidad;

        $dmodel = new VentaFacturaSolicitudDetalle();
        $dmodel->venta = $data->id;
        $dmodel->producto = $producto->id;
        $dmodel->cantidad = $value->cantidad;
        $dmodel->precio = $producto->precio_publico;
        $dmodel->save();
        if($producto->maneja_inventario == "1"){
          $dmodelData = $dmodel->getAttributes();
          $almacenMovimiento = new AlmacenMovimiento();
          $almacenMovimiento->almacen = $almacen->id;
          $almacenMovimiento->producto = $producto->id;
          $almacenMovimiento->tipo = "salida";
          $almacenMovimiento->cantidad = $value->cantidad;
          $almacenMovimiento->referencia = $data->id;
          $almacenMovimiento->save();

          $inventario = new Inventario("WHERE producto = ".$producto->id." AND almacen = ".$almacen->id);
          if($inventario->id != ""){
            $inventario->cantidad_actual = $inventario->cantidad_actual - $value->cantidad;
            $inventario->save();
          }
          

        }
      }
    }

    if($this->error == ""){
      $movimiento = new VentaFacturaSolicitudMovimiento("WHERE venta = ".$model->id." AND tipo = 'ingreso' AND numero = '1'");
      $movimiento->venta = $dataVentaFacturaSolicitud->id;
      $movimiento->user = $this->user->id;
      $movimiento->tipo = 'ingreso';
      $movimiento->monto = $model->anticipo;
      $movimiento->forma_pago = $model->forma_pago;
      $movimiento->save();
    }

    if($this->error == ""){
      $aQuery = ProductoTemporalEdit::model()->executeQuery("DELETE FROM ec_producto_temporal_edit WHERE user = ".$this->user->id);
    }

    $this->renderJSON($model->getAttributes());
  }

  public function actionVerificarDuplicado(){
    $this->template = null;
    $aSalida = array();
    $aSalida["exito"] = false;
    $rows = Cliente::model()->findAll("WHERE telefono = '".$this->params["telefono"]."' OR correo = '".$this->params["correo"]."'");
    $aSalida["duplicados"] = $rows;
    if(count($rows) > 0){
      $aSalida["exito"] = true;
    }
    $this->renderJSON($aSalida);
  }

  public function actionReparaVentaFacturaSolicituds(){
    $this->template = null;
    return;
    $rows = VentaFacturaSolicitud::model()->findAll("WHERE id >= 19 AND tipo = 'venta' AND created LIKE '%2024-11-17%'");
    foreach ($rows as $key => $value) {
      $model = new VentaFacturaSolicitudMovimiento();
      $model->venta = $value->id;
      $model->forma_pago = $value->forma_pago;
      $model->monto = $value->anticipo;
      $model->tipo = 'ingreso';
      $model->user = $value->user;
      $model->created = $value->created;
      if(!$model->save()){
        $this->error .= "error ".$model->error;
      }
    }
    $this->renderJSON($rows);
  }

  public function actionReparaClientes(){
    $this->template = null;
    $rows = Cliente::model()->findAll("WHERE estatus = 1");
    foreach ($rows as $key => $value) {
      $ventas = VentaFacturaSolicitud::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE cliente = ".$value->id)[0]->total ?? 0;
      if($ventas == 0){
        $model = new Cliente($value->id);
        $model->estatus = 2;
        $model->save();
      }
    }
    $this->renderJSON();
  }

  public function actionReparaAlmacen2(){
    $this->template = null;
    $rows = VentaFacturaSolicitud::model()->findAll("WHERE tipo = 'cotizacion'");
    //$this->error = count($rows)."WHERE tipo == 'cotizacion'";
    foreach ($rows as $key => $value) {
      $aux = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE tipo = 'salida' AND referencia = '".$value->id."'");
    }
    $this->renderJSON($rows);
  }

  public function actionSaveAbono(){
    $this->template = null;
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $abonos = VentaFacturaSolicitudMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total ?? 0;
    $saldoReal = $data->saldo - $abonos;
    if($this->params["abono"] > $saldoReal){
      $this->error .= "No puedes abonar más del saldo pendiente.";
    }


    if($saldoReal == 0){
      $this->error .= "La nota ya está pagada.";
    }

    if(floatval($this->params["abono"]) == 0){
      $this->error .= "El monto no puede ser 0";
    }
    

    if($this->error == ""){
      $previos = VentaFacturaSolicitudMovimiento::model()->findAll("WHERE venta = ".$data->id."");
      $siguiente = count($previos) + 1;
      $model = new VentaFacturaSolicitudMovimiento();
      $model->venta = $data->id;
      $model->monto = $this->params["abono"];
      $model->saldo = $saldoReal - $this->params["abono"];
      $model->tipo = 'ingreso';
      $model->user = $this->user->id;
      $model->forma_pago = $this->params["forma_pago"];
      $model->numero = $siguiente;
      $model->save();
    }
    $this->renderJSON();
  }

  public function actionSaveEstatus(){
    $this->template = null;
    $aSalida = array();
    $data = new VentaFacturaSolicitud($this->params["id"]);
    $aSalida["data"] = $data;
    $aSalida["params"] = $this->params;


    if($this->error == ""){
      $data->estatus = $this->params["estatus"];
      $data->save();
    }
    $this->renderJSON($aSalida);
  }

  public function actionImprimeTicket2(){
    $model = new VentaFacturaSolicitud($this->params["id"]);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;
    $detalle = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = $model->id");
    $sucursal = new Sucursal($model->sucursal);
    $operador = new Operador("WHERE user = ".$model->user);
    foreach ($detalle as $key => $value) {
      $prod = new Producto($value->producto);
      $detalle[$key]->nombre_producto = $prod->nombre;
    }

    // Crear y generar PDF
    $pdf = new PDF_Ticket();
    $pdf->AddPage();
    $pdf->headerTicket($sucursal);
    $pdf->contactInfoTable($model, $sucursal);
    $pdf->productTable($detalle);
    $pdf->paymentSummary($model, $operador);
    $pdf->footerTicket($sucursal);

    $pdf->Output();
  }

  public function actionImprimeTicket(){
    $model = new VentaFacturaSolicitud($this->params["id"]);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;
    $detalle = VentaFacturaSolicitudDetalle::model()->findAll("WHERE venta = $model->id");
    $sucursal = new Sucursal($model->sucursal);
    $operador = new Operador("WHERE user = ".$model->user);
    foreach ($detalle as $key => $value) {
      $prod = new Producto($value->producto);
      $detalle[$key]->nombre_producto = $prod->nombre;
    }

    $abonos = VentaFacturaSolicitudMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$model->id)[0]->total;
    $model->abonos = $abonos;
    // Crear y generar PDF
    $pdf = new PDF_Ticket();
    $pdf->AddPage();
    $pdf->headerTicket($sucursal);
    $pdf->contactInfoTable($model, $sucursal);
    $pdf->productTable($detalle);
    $pdf->paymentSummary($model, $operador);
    $pdf->footerTicket($sucursal);

    if($model->tipo !== 'cotizacion' && $model->cliente != "1"){
      $pdf->AddPage('P', [80, 385]);
      $pdf->headerTicket($sucursal);
      $pdf->contactInfoTable($model, $sucursal);
      $pdf->productTable($detalle);
      $pdf->paymentSummary($model, $operador);
      $pdf->graduationTable2($model);
      $pdf->footerTicket($sucursal);
    }


    $pdf->Output();
  }

  public function actionImprimeTicketAbono() {
    $modelMovimiento = new VentaFacturaSolicitudMovimiento($this->params["id"]);
    $model = new VentaFacturaSolicitud($modelMovimiento->venta);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;

    $abonos = VentaFacturaSolicitudMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE numero != 1 AND tipo = 'ingreso' AND venta = " . $model->id)[0]->total ?? 0;
    $nuevoSaldo = $model->saldo - $abonos;
    $nuevoSaldo = $modelMovimiento->saldo ?? 0;

    $sucursal = new Sucursal($model->sucursal);

    $pdf = new PDF_AbonoTicket();
    $pdf->AddPage();
    $pdf->headerTicket($sucursal);
    $pdf->contactInfoTableAbono($model, $sucursal, $modelMovimiento);
    $pdf->abonoDetails($cliente, $modelMovimiento, $nuevoSaldo);
    $pdf->footerTicket($sucursal);
    $pdf->Output();
  }

}

