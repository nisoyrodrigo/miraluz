<?php
require_once('lib/FPDI/src/autoload.php');
use setasign\Fpdi\Fpdi;

class VentaController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionCotizaciones(){
    $this->render("cotizaciones");
  }

  public function actionVentas(){
    $this->render("ventas");
  }

  public function actionApartados(){
    $this->render("apartados");
  }

  public function actionEnviarLaboratorio(){
    $this->render("enviarlaboratorio");
  }

  public function actionLaboratorio(){
    $this->render("laboratorio");
  }

  public function actionSucursal(){
    $this->render("sucursal");
  }

  public function actionEntregados(){
    $this->render("entregados");
  }

  public function actionAgregar(){
    $this->render("agregar");
  }

  public function actionEditar(){
    $data = new Venta($this->params["data"]);
    $this->render("editar", array("data"=>$data));
  }

  public function actionEditarCotizacion(){
    $data = new Venta($this->params["data"]);
    $this->render("editar-cotizacion", array("data"=>$data));
  }

  public function actionDemo(){
    $this->render("demo");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Cotizacion($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionDetalle(){
    $this->template = null;
    $data = new Venta($this->params["id"]);
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
    $data = new Venta($this->params["id"]);
    $this->render("edit-abono", array("data"=>$data));
  }

  public function actionEditEstatus(){
    $this->template = null;
    $data = new Venta($this->params["id"]);
    $this->render("edit-estatus", array("data"=>$data));
  }

  public function actionEditReceta(){
    $this->template = null;
    $data = new Venta($this->params["id"]);
    $this->render("edit-receta", array("data"=>$data));
  }

  public function actionSaveReceta(){
    $this->template = null;

    $id = intval($this->params["id"] ?? 0);
    if($id <= 0){
      $this->error = "ID de venta no valido";
      return $this->renderJSON();
    }

    $data = new Venta($id);
    if(empty($data->id)){
      $this->error = "Venta no encontrada";
      return $this->renderJSON();
    }

    // Actualizar campos de receta
    $data->od_av_con_lentes = $this->params["od_av_con_lentes"] ?? '';
    $data->od_av_sin_lentes = $this->params["od_av_sin_lentes"] ?? '';
    $data->od_av_con_correccion = $this->params["od_av_con_correccion"] ?? '';
    $data->oi_av_con_lentes = $this->params["oi_av_con_lentes"] ?? '';
    $data->oi_av_sin_lentes = $this->params["oi_av_sin_lentes"] ?? '';
    $data->oi_av_con_correccion = $this->params["oi_av_con_correccion"] ?? '';
    $data->tiene_miopia = intval($this->params["tiene_miopia"] ?? 0);
    $data->tiene_hipermetropia = intval($this->params["tiene_hipermetropia"] ?? 0);
    $data->tiene_astigmatismo = intval($this->params["tiene_astigmatismo"] ?? 0);
    $data->observaciones = $this->params["observaciones"] ?? '';
    $data->diagnostico = $this->params["diagnostico"] ?? '';
    $data->tratamiento = $this->params["tratamiento"] ?? '';
    $data->proxima_cita = !empty($this->params["proxima_cita"]) ? $this->params["proxima_cita"] : null;

    if(!$data->save()){
      $this->error = $data->error ?: "Error al guardar";
      return $this->renderJSON();
    }

    $this->renderJSON(["success" => true, "id" => $data->id]);
  }

  public function actionConfirmaVendedor(){
    $this->template = null;
    $this->render("confirma-vendedor", array("sucursal" => $this->params["sucursal"], "vendedor" => $this->params["vendedor"]));
  }
  
  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    $rows = Cotizacion::model()->findAll("WHERE estatus = 1 ".$aWhere);
    foreach ($rows as $key => $value) {
    }
    $this->renderJSON($rows);
  }


  public function actionGetAllCotizaciones(){
    $template = null;
    $aWhere = "";
    $operador = new Operador("WHERE user = ".$this->user->id);
    $rows = Venta::model()->findAll("WHERE tipo = 'cotizacion' AND sucursal IN(".$operador->sucursales.") ".$aWhere);
    foreach ($rows as $key => $value) {
      $cliente = new Cliente($value->cliente);
      $productos = VentaDetalle::model()->findAll("WHERE venta = ".$value->id);
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


  public function actionGetAllVentas(){
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
    $rows = Venta::model()->findAll("WHERE tipo = 'venta' AND sucursal IN(".$operador->sucursales.") ".$aWhere);

    $count = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    $rows = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    //$rowsquery = Venta::model()->executeQuery("SELECT v.*, c.nombre, c.telefono, GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos_descripcion FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $aWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"]);
      
    $rowsquery = Venta::model()->executeQuery("
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
      $productos = VentaDetalle::model()->findAll("WHERE venta = ".$value->id);
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

      $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$value->id)[0]->total;
      // $rowsquery[$key]->abonos = $abonos ?? 0;
      $rowsquery[$key]->saldo = $value->saldo - $abonos;
      $estatus = new VentaEstatus($value->estatus);
      $rowsquery[$key]->estatus_descripcion = $estatus->nombre;
    }

    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($rows[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);
  }

  public function actionGetAllPorEstatus(){
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
    $rows = Venta::model()->findAll("WHERE estatus = ".$this->params["e"]." AND tipo = 'venta' AND sucursal IN(".$operador->sucursales.") ".$aWhere);

    $count = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.estatus = ".$this->params["e"]." AND v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    $rows = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.estatus = ".$this->params["e"]." AND v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    //$rowsquery = Venta::model()->executeQuery("SELECT v.*, c.nombre, c.telefono, GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos_descripcion FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $aWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"]);
      
    $rowsquery = Venta::model()->executeQuery("
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
        WHERE v.estatus = ".$this->params["e"]." AND v.tipo = 'venta' 
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
      $productos = VentaDetalle::model()->findAll("WHERE venta = ".$value->id);
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

      $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$value->id)[0]->total;
      // $rowsquery[$key]->abonos = $abonos ?? 0;
      $rowsquery[$key]->saldo = $value->saldo - $abonos;
      $estatus = new VentaEstatus($value->estatus);
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
    $model = new VentaMovimiento($this->params["id"]);
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

  public function actionSaveVenta(){
    $this->template = null;
    $model = new Venta();
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->clave = $this->generarClave(5);
    }

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
    $consecutivo = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE tipo = '$model->tipo' AND sucursal = $model->sucursal AND DATE_FORMAT(created, '%Y%m') = '$fechaActual'")[0]->total + 1;
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
      
      if ($model->tipo === "venta" || $model->tipo === "v") {

        $porcentaje = $model->anticipo / $model->total;

        if ($porcentaje >= 0.30 && $porcentaje <= 0.30) {
          // EXACTAMENTE 30%
          $model->estatus = 7; // Apartado
        } elseif ($porcentaje > 0.30) {
          // MÁS del 30%
          $model->estatus = 8; // Enviar a laboratorio
        } else {
          // Menos del 30% (aunque por validación casi no debería pasar)
          $model->estatus = 7; // Apartado
        }
      }

      if($this->params["cliente"] == "1"){
        $model->estatus = 4;
      }

      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataVenta = $model->getAttributes();
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

        $dmodel = new VentaDetalle();
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
      $movimiento = new VentaMovimiento();
      $movimiento->venta = $dataVenta->id;
      $movimiento->user = $this->user->id;
      $movimiento->tipo = 'ingreso';
      $movimiento->monto = $model->anticipo;
      $movimiento->forma_pago = $model->forma_pago;

      if ($model->forma_pago === 'tarjeta' || $model->forma_pago === 'tarjetac') {
        $movimiento->banco = !empty($model->banco) ? $model->banco : "";
        $movimiento->tarjeta_digitos = !empty($model->tarjeta_digitos) ? $model->tarjeta_digitos : "";
      } else {
        $movimiento->banco = "";
        $movimiento->tarjeta_digitos = "";
      }

      $movimiento->save();
    }

    if($this->error == ""){
      $aQuery = ProductoTemporal::model()->executeQuery("DELETE FROM ec_producto_temporal WHERE user = ".$this->user->id);
    }

    $this->renderJSON($model->getAttributes());
  }



  public function actionSaveVentaEdit(){
    $this->template = null;
    $model = new Venta($this->params["venta_id"]);
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
        $dataVenta = $model->getAttributes();
      }
    }

    if($this->error == ""){
      $data = $model->getAttributes();
      $eliminaMovimientosAlmacen = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE referencia = '".$data->id."'");
      $detallePrevio = VentaDetalle::model()->findAll("WHERE venta = ".$data->id);
      foreach ($detallePrevio as $key => $value) {
        $inv = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        $inv->cantidad_actual = $inv->cantidad_actual + $value->cantidad;
        $inv->save();
      }
      $eliminaDetalle = VentaDetalle::model()->executeQuery("DELETE FROM ec_venta_detalle WHERE venta = '".$data->id."'");
      foreach ($rows as $key => $value) {
        $producto = new Producto($value->producto);
        $rows[$key]->nombre = $producto->nombre;
        $rows[$key]->familia = $producto->familia;
        $rows[$key]->precio_publico = $producto->precio_publico;
        $rows[$key]->total = $producto->precio_publico * $value->cantidad;

        $dmodel = new VentaDetalle();
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
      $movimiento = new VentaMovimiento("WHERE venta = ".$model->id." AND tipo = 'ingreso' AND numero = '1'");
      $movimiento->venta = $dataVenta->id;
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
    $model = new Venta($this->params["venta_id"]);
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
      $consecutivo = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE tipo = 'venta' AND sucursal = $model->sucursal AND DATE_FORMAT(created, '%Y%m') = '$fechaActual'")[0]->total + 1;
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
        $dataVenta = $model->getAttributes();
      }
    }

    if($this->error == ""){
      $data = $model->getAttributes();
      $eliminaMovimientosAlmacen = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE referencia = '".$data->id."'");
      $detallePrevio = VentaDetalle::model()->findAll("WHERE venta = ".$data->id);
      foreach ($detallePrevio as $key => $value) {
        $inv = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        $inv->cantidad_actual = $inv->cantidad_actual + $value->cantidad;
        $inv->save();
      }
      $eliminaDetalle = VentaDetalle::model()->executeQuery("DELETE FROM ec_venta_detalle WHERE venta = '".$data->id."'");
      foreach ($rows as $key => $value) {
        $producto = new Producto($value->producto);
        $rows[$key]->nombre = $producto->nombre;
        $rows[$key]->familia = $producto->familia;
        $rows[$key]->precio_publico = $producto->precio_publico;
        $rows[$key]->total = $producto->precio_publico * $value->cantidad;

        $dmodel = new VentaDetalle();
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
      $movimiento = new VentaMovimiento("WHERE venta = ".$model->id." AND tipo = 'ingreso' AND numero = '1'");
      $movimiento->venta = $dataVenta->id;
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

  public function actionReparaVentas(){
    $this->template = null;
    $rows = Venta::model()->findAll("WHERE id >= 1 AND tipo = 'venta' AND clave IS NULL LIMIT 2000");
    foreach ($rows as $key => $value) {
      $model = new Venta($value->id);
      $model->clave = $this->generarClave(5);
      if(!$model->save()){
        $this->error .= $model->id."-> error ".$model->error."<br>";
      }
    }
    $this->renderJSON($rows);
  }

  private function generarClave($length = 5){
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $clave = '';

    for ($i = 0; $i < $length; $i++) {
      $clave .= $chars[random_int(0, strlen($chars) - 1)];
    }

    return $clave;
  }

  public function actionReparaClientes(){
    $this->template = null;
    $rows = Cliente::model()->findAll("WHERE estatus = 1");
    foreach ($rows as $key => $value) {
      $ventas = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta WHERE cliente = ".$value->id)[0]->total ?? 0;
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
    $rows = Venta::model()->findAll("WHERE tipo = 'cotizacion'");
    //$this->error = count($rows)."WHERE tipo == 'cotizacion'";
    foreach ($rows as $key => $value) {
      $aux = AlmacenMovimiento::model()->executeQuery("DELETE FROM ec_almacen_movimiento WHERE tipo = 'salida' AND referencia = '".$value->id."'");
    }
    $this->renderJSON($rows);
  }

  public function actionSaveAbono(){
    $this->template = null;
    $data = new Venta($this->params["id"]);
    $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total ?? 0;
    $saldoReal = $data->saldo - $abonos;
    $abonoNuevo = floatval($this->params["abono"]);

    if ($abonoNuevo > $saldoReal) {
      $this->error .= "No puedes abonar más del saldo pendiente.";
    }

    if($saldoReal == 0){
      $this->error .= "La nota ya está pagada.";
    }

    if ($abonoNuevo <= 0) {
      $this->error .= "El monto no puede ser 0.";
    }

    if ($this->error != "") {
      $this->renderJSON();
      return;
    }

    // =========================
    // GUARDA ABONO (TU CÓDIGO)
    // =========================
    if($this->error == ""){
      $previos = VentaMovimiento::model()->findAll("WHERE venta = ".$data->id."");
      $siguiente = count($previos) + 1;

      $model = new VentaMovimiento();
      $model->venta = $data->id;
      $model->monto = $abonoNuevo;
      $model->saldo = $saldoReal - $abonoNuevo;
      $model->tipo = 'ingreso';
      $model->user = $this->user->id;
      $model->forma_pago = $this->params["forma_pago"];
      $model->banco = $this->params["banco"];
      $model->tarjeta_digitos = $this->params["tarjeta_digitos"];
      $model->numero = $siguiente;
      $model->save();
    }

    // =========================
    // LÓGICA DE ESTATUS (NUEVO)
    // =========================
    $totalPagado = $data->anticipo + $abonos + $abonoNuevo;

    // ➜ Si supera 30% y estaba NUEVA o APARTADA → LABORATORIO (8)
    if (
      $totalPagado >= ($data->total * 0.30) &&
      ($data->estatus == "1" || $data->estatus == "7")
    ) {
      $data->estatus = 8; // Enviar a laboratorio
    }

    // ➜ Si se LIQUIDA
    if ($totalPagado >= $data->total) {
      if (!empty($this->params["entregar"]) && $this->params["entregar"] == "1") {
        $data->estatus = 4; // Entregado
      }
    }

    $data->save();

    $this->renderJSON();

  }

  /**
   * Imprime la receta óptica de una venta
   * Basado en el formato del Excel RECETA__1_.xlsx
   */
  public function actionImprimeReceta(){
    $this->template = null;
    $model = new Venta($this->params["id"]);
    $cliente = new Cliente($model->cliente);
    $sucursal = new Sucursal($model->sucursal);
    $operador = new Operador("WHERE user = ".$model->user);

    // Crear PDF de receta
    $pdf = new PDF_Receta();
    $pdf->AddPage();
    $pdf->recetaCompleta($model, $cliente, $sucursal, $operador);

    $folio = $model->folio ?? $model->id;
    $nombreArchivo = 'receta_' . $folio . '.pdf';
    $pdf->Output('I', $nombreArchivo);
  }

  public function actionSaveEstatus(){
    $this->template = null;
    $aSalida = array();
    $data = new Venta($this->params["id"]);
    $aSalida["data"] = $data;
    $aSalida["params"] = $this->params;

    $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total ?? 0;

    $saldoReal = $data->saldo - $abonos;

    if($this->params["estatus"] == "4" && $saldoReal > 0){
      $this->error .= "No puedes entrega el producto porque aún tiene saldo pendiente de $".number_format($saldoReal,2);
    }


    if($this->error == "" && $data->tipo == "venta" && $data->estatus != "3" && $this->params["estatus"] == "3" && $data->notificado_sms == "0"){
      $mensajeSms = "Óptica Miraluz: Tu pedido ".$data->folio." ya está en sucursal. Pasa a recogerlo con tu ticket. ¡Gracias por tu compra!";
      $aSalida["smsmsg"] = $mensajeSms;
      $cliente = new Cliente($data->cliente);
      $aSalida["cliente"] = $cliente;
      if(strlen($cliente->telefono) === 10){
        if($cliente->id != "1"){
          //$data->notificado_sms = $data->notificado_sms + 1;
          //$asms = $this->sendSMSAWS("+52".$cliente->telefono,$mensajeSms);
          //$aSalida["sms"] = $asms;
        }
      }
    }

    if($this->error == ""){
      $data->estatus = $this->params["estatus"];
      $data->estatus_garantia = $this->params["estatus_garantia"];


      if ($this->params["estatus"] == "5") { // Suponiendo que 5 es Garantía
        $data->gtipo_vision = $this->params["gtipo_vision"] ?? "vision_sencilla";
        $data->god_esfera = $this->params["god_esfera"] ?? "0";
        $data->god_cilindro = $this->params["god_cilindro"] ?? "0";
        $data->god_eje = $this->params["god_eje"] ?? "0";
        $data->god_add = $this->params["god_add"] ?? "0";
        $data->god_dnp = $this->params["god_dnp"] ?? "0";
        $data->god_altura = $this->params["god_altura"] ?? "0";

        $data->goi_esfera = $this->params["goi_esfera"] ?? "0";
        $data->goi_cilindro = $this->params["goi_cilindro"] ?? "0";
        $data->goi_eje = $this->params["goi_eje"] ?? "0";
        $data->goi_add = $this->params["goi_add"] ?? "0";
        $data->goi_dnp = $this->params["goi_dnp"] ?? "0";
        $data->goi_altura = $this->params["goi_altura"] ?? "0";
      }


      $data->save();
    }
    $this->renderJSON($aSalida);
  }

  public function actionImprimeTicket2(){
    $model = new Venta($this->params["id"]);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;
    $detalle = VentaDetalle::model()->findAll("WHERE venta = $model->id");
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
    $model = new Venta($this->params["id"]);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;
    $detalle = VentaDetalle::model()->findAll("WHERE venta = $model->id");
    $sucursal = new Sucursal($model->sucursal);
    $operador = new Operador("WHERE user = ".$model->user);
    foreach ($detalle as $key => $value) {
      $prod = new Producto($value->producto);
      $detalle[$key]->nombre_producto = $prod->nombre;
    }

    $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$model->id)[0]->total;
    $model->abonos = $abonos;
    // Crear y generar PDF
    $pdf = new PDF_Ticket();
    $pdf->AddPage();
    $pdf->headerTicket($sucursal);
    $pdf->contactInfoTable($model, $sucursal);
    $pdf->productTable($detalle);
    $pdf->paymentSummary($model, $operador);

    if($model->tipo !== 'cotizacion'){
      $pdf->footerTicketNV($sucursal, $model);
    } else {
      $pdf->footerTicket($sucursal);
    }

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
    $modelMovimiento = new VentaMovimiento($this->params["id"]);
    $model = new Venta($modelMovimiento->venta);
    $cliente = new Cliente($model->cliente);
    $model->nombre_cliente = $cliente->nombre;

    $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE numero != 1 AND tipo = 'ingreso' AND venta = " . $model->id)[0]->total ?? 0;
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

class PDF_Ticket extends FPDF
{

    function __construct() {
        parent::__construct('P', 'mm', array(80, 345)); // Cambié el alto a 150mm para adaptarlo mejor a una página
    }

    function headerTicket($sucursal)
    { 
        $pageWidth = $this->GetPageWidth();
        $this->Image('images/optica_logo.jpg', 5, 5, $pageWidth - 10); // Imagen centrada y con ancho completo
        $this->Ln(35); // Espacio después del logo

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, 'SUCURSAL '.$sucursal->clave, 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $aTexto = $sucursal->nombre."\n".$sucursal->direccion."\n".$sucursal->correo;
        $this->MultiCell(0, 4, utf8_decode($aTexto), 0, 'C');
        $this->Ln(5);
    }

    function contactInfoTable($model, $sucursal)
    {
        $this->SetX(10); // Posiciona hacia el centro
        $this->SetFont('Arial', 'B', 10);
        
        // Teléfono
        $this->Cell(30, 4, utf8_decode('TELÉFONO'), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->telefono), 0, 'R'); // Permite que el texto del teléfono se muestre en varias líneas si es necesario

        // Horario
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'HORARIO', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->horario), 0, 'R'); // Permite que el texto del horario se muestre en varias líneas si es necesario
        $this->Ln(3);

        // Nota, Cliente y Fecha
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'NOTA #', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 4, $model->folio, 0, 1, 'R');

        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'CLIENTE', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode($model->nombre_cliente), 0, 'R');

        $this->Ln(3);
        $this->SetFont('Arial', '', 10);
        if ($model->tipo !== 'cotizacion') {
          $this->Cell(0, 4, date('d/m/Y', strtotime($model->fecha_venta)), 0, 1, 'C'); // Centra la fecha
        } else {
          $this->Cell(0, 4, date('d/m/Y', strtotime($model->created)), 0, 1, 'C'); // Centra la fecha
        }
        $this->Ln(3);
    }

    function productTable($detalle)
    {
        $this->SetX(2);
        $this->SetFont('Arial', 'B', 9);

        // Encabezados con bordes
        $this->Cell(40, 4, 'PRODUCTO', 1, 0, 'L');
        $this->Cell(15, 4, 'CANT', 1, 0, 'C');
        $this->Cell(21, 4, 'SUBTOTAL', 1, 1, 'R');

        $this->SetFont('Arial', '', 10);
        foreach ($detalle as $item) {
            $this->SetX(2);

            // MultiCell para el nombre del producto sin bordes
            $this->MultiCell(40, 4, utf8_decode($item->nombre_producto), 0, 'L');

            // Ajustar posición para "CANT" y "SUBTOTAL" en la misma línea, sin bordes
            $y = $this->GetY();
            $this->SetXY(42, $y - 4);
            $this->Cell(15, 4, $item->cantidad, 0, 0, 'C');
            $this->Cell(21, 4, '$' . number_format($item->cantidad * $item->precio, 2), 0, 1, 'R');
        }
        $this->Ln(5);
    }

    function paymentSummary($model, $operador)
    {
        $this->SetX(2);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(46, 4, 'SUBTOTAL', 0, 0, 'L');
        $this->Cell(30, 4, '$' . number_format($model->subtotal, 2), 0, 1, 'R');

        if ($model->tipo !== 'cotizacion') {
            // Solo imprime descuento, anticipo y saldo si es una venta
            $this->SetX(2);
            $this->Cell(46, 4, 'DESCUENTO', 0, 0, 'L');
            $this->Cell(30, 4, '$' . number_format($model->descuento, 2), 0, 1, 'R');
        }

        $this->SetX(2);
        $this->Cell(46, 4, 'TOTAL', 0, 0, 'L');
        $this->Cell(30, 4, '$' . number_format($model->total, 2), 0, 1, 'R');

        if ($model->tipo !== 'cotizacion') {

            $formaPago = "";
              switch ($model->forma_pago) {
                case 'efectivo':
                    $formaPago = 'Efectivo';
                    break;
                case 'tarjeta':
                    $formaPago = 'Tarjeta Débito';
                    break;
                case 'tarjetac':
                    $formaPago = 'Tarjeta Crédito';
                    break;
                case 'vales':
                    $formaPago = 'Vales';
                    break;
                default:
                    $formaPago = 'Desconocido';
                    break;
            }

            $this->SetX(2);
            $this->Cell(46, 4, 'ANTICIPO ('.$formaPago.')', 0, 0, 'L');
            $this->Cell(30, 4, '$' . number_format($model->anticipo, 2), 0, 1, 'R');

            if ($model->tipo === 'venta') {
              $this->SetX(2);
              $this->Cell(46, 4, 'ABONOS', 0, 0, 'L');
              $this->Cell(30, 4, '$' . number_format($model->abonos, 2), 0, 1, 'R');
            }

            $this->SetX(2);
            $this->SetFont('Arial', '', 9);
/*
            $this->Cell(46, 4, 'Forma de pago', 0, 0, 'L');
            $this->Cell(30, 4, $formaPago, 0, 1, 'R');
            // $this->Cell(30, 4, utf8_decode("$formaPago"), 0, 1, 'R'); // Texto alineado a la izquierda
            $this->Ln(4);*/
            /*
            $this->SetFont('Arial', 'B', 10);
            $this->SetX(2);
            $this->Cell(46, 4, 'SALDO', 0, 0, 'L');
            $this->Cell(30, 4, '$' . number_format($model->saldo, 2), 0, 1, 'R');
            $this->Ln(8);
            */

            $this->SetX(2);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(46, 4, 'SALDO', 0, 0, 'L');
            $saldoRestante = $model->saldo - ($model->abonos ?? 0); // Resta los abonos al saldo
            $this->Cell(30, 4, '$' . number_format($saldoRestante, 2), 0, 1, 'R');
            $this->Ln(8);

        }

        if ($model->tipo !== 'cotizacion') {
            // Solo imprime la fecha de entrega si es una venta
            $fechaEntrega = date('d/m/Y', strtotime($model->fecha_venta . ' +10 days'));
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 4, utf8_decode("FECHA DE ENTREGA"), 0, 1, 'C'); // Encabezado centrado
            $this->SetFont('Arial', '', 10);
            if($model->estatus != "6"){
              $this->Cell(0, 4, $fechaEntrega, 0, 1, 'C'); // Fecha centrada
            } else {
              $this->Cell(0, 4, "CANCELADA", 0, 1, 'C'); // Fecha centrada
            }
            $this->Ln(8);
        }

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 4, utf8_decode("ATENDIÓ"), 0, 1, 'C'); // Encabezado centrado
        $this->SetFont('Arial', '', 10);
        $operadorNombre = $operador->nombre." ".$operador->apaterno." ".$operador->amaterno;
        $this->Cell(0, 4, utf8_decode($operadorNombre), 0, 1, 'C'); // Fecha centrada
        $this->Ln(10);
    }

    function footerTicket($sucursal)
    {
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode("DESPUÉS DE 30 DÍAS NO RESPONDEMOS POR TRABAJOS NO RECOGIDOS: NO HAY DEVOLUCIÓN DE ANTICIPOS"), 0, 'C');
        $this->Ln(2);

        $this->MultiCell(0, 4, utf8_decode("CONSERVA TU TICKET PARA CUALQUIER ACLARACIÓN"), 0, 'C');
        $this->Ln(2);

        $this->MultiCell(0, 4, utf8_decode("TODOS LOS LENTES DE CONTACTO SON UN PRODUCTO EXTERNO ATIENDE LAS INDICACIONES Y REVISA CADUCIDAD"), 0, 'C');
        $this->Ln(2);

        $this->MultiCell(0, 4, utf8_decode("TODAS LAS PROMOCIONES APLICAN EN LA COMPRA DE POLICARBONATO + ARMAZÓN *APLICA RESTRICCIONES CONSULTA EN SUCURSAL"), 0, 'C');
        $this->Ln(2);

        $this->MultiCell(0, 4, utf8_decode("DUDAS Y COMENTARIOS: ".$sucursal->telefono_dudas), 0, 'C');
        $this->Ln(2);

        $this->MultiCell(0, 4, utf8_decode("¿SOLICITAS FACTURA? ENVÍA TUS DATOS AL: ".$sucursal->telefono_factura), 0, 'C');
        $this->Ln(2);
    }

    function footerTicketNV($sucursal, $model)
    {
      $this->SetFont('Arial', '', 10);

      $this->MultiCell(0, 4, utf8_decode("DESPUÉS DE 30 DÍAS NO RESPONDEMOS POR TRABAJOS NO RECOGIDOS: NO HAY DEVOLUCIÓN DE ANTICIPOS"), 0, 'C');
      $this->Ln(2);

      $this->MultiCell(0, 4, utf8_decode("CONSERVA TU TICKET PARA CUALQUIER ACLARACIÓN"), 0, 'C');
      $this->Ln(2);

      $this->MultiCell(0, 4, utf8_decode("TODOS LOS LENTES DE CONTACTO SON UN PRODUCTO EXTERNO ATIENDE LAS INDICACIONES Y REVISA CADUCIDAD"), 0, 'C');
      $this->Ln(2);

      $this->MultiCell(0, 4, utf8_decode("TODAS LAS PROMOCIONES APLICAN EN LA COMPRA DE POLICARBONATO + ARMAZÓN *APLICA RESTRICCIONES CONSULTA EN SUCURSAL"), 0, 'C');
      $this->Ln(4);

      // --- NUEVO BLOQUE: SEGUIMIENTO ---
      $this->SetFont('Arial', 'B', 10);
      $this->MultiCell(0, 4, utf8_decode("¿SOLICITAS FACTURA O SEGUIMIENTO DE TU PEDIDO?"), 0, 'C');

      $this->SetFont('Arial', '', 10);
      $this->Cell(0, 5, utf8_decode("Entra a:"), 0, 1, 'C');

      // OJO: sin https para que quepa; si quieres pon https://
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(0, 5, utf8_decode("https://pos.opticamiraluz.com/seguimiento"), 0, 1, 'C');
      $this->Ln(2);

      // Folio y clave
      //$this->SetFont('Arial', '', 10);
      //$this->Cell(0, 5, utf8_decode("Folio: ".$model->folio), 0, 1, 'C');
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(0, 6, utf8_decode("Clave: ".$model->clave), 0, 1, 'C');
      $this->Ln(2);
    }

    function graduationTable($model)
    {
      $this->Ln(5);
      $this->SetFont('Arial', 'B', 10);
      $this->Cell(0, 10, utf8_decode('Graduación'), 0, 1, 'C');
      $this->Ln(2);

      $this->SetX(1);
      // Encabezados
      $this->SetFont('Arial', 'B', 8);
      $this->Cell(6, 6, 'Ojo', 1);
      $this->Cell(12, 6, 'Esfera', 1);
      $this->Cell(12, 6, 'Cil', 1);
      $this->Cell(12, 6, 'Eje', 1);
      $this->Cell(12, 6, 'ADD', 1);
      $this->Cell(12, 6, 'DNP', 1);
      $this->Cell(12, 6, 'Altura', 1);
      $this->Ln();

      $this->SetX(1);
      // OD (Ojo derecho)
      $this->SetFont('Arial', '', 9);
      $this->Cell(6, 6, 'OD', 1);
      $this->Cell(12, 6, $model->od_esfera, 1);
      $this->Cell(12, 6, $model->od_cilindro, 1);
      $this->Cell(12, 6, $model->od_eje, 1);
      $this->Cell(12, 6, $model->od_add, 1);
      $this->Cell(12, 6, $model->od_dnp, 1);
      $this->Cell(12, 6, $model->od_altura, 1);
      $this->Ln();

      $this->SetX(1);
      // OI (Ojo izquierdo)
      $this->Cell(6, 6, 'OI', 1);
      $this->Cell(12, 6, $model->oi_esfera, 1);
      $this->Cell(12, 6, $model->oi_cilindro, 1);
      $this->Cell(12, 6, $model->oi_eje, 1);
      $this->Cell(12, 6, $model->oi_add, 1);
      $this->Cell(12, 6, $model->oi_dnp, 1);
      $this->Cell(12, 6, $model->oi_altura, 1);
      $this->Ln(10);
    }

    function graduationTable2($model)
    {
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, utf8_decode('Graduación'), 0, 1, 'C');
        $this->Ln(2);

        $this->SetX(1);
        $this->SetFont('Arial', 'B', 8);

        // Encabezados de la tabla
        $this->Cell(6, 6, 'Ojo', 1);
        $this->Cell(12, 6, 'Esfera', 1);
        $this->Cell(12, 6, 'Cil', 1);
        $this->Cell(12, 6, 'Eje', 1);
        if ($model->tipo_vision !== 'vision_sencilla') {
            $this->Cell(12, 6, 'ADD', 1);
            $this->Cell(12, 6, 'Altura', 1);
        }
        $this->Cell(12, 6, 'DNP', 1);
        $this->Ln();

        // Filas de la tabla: Ojo derecho (OD)
        $this->SetX(1);
        $this->SetFont('Arial', '', 9);
        $this->Cell(6, 6, 'OD', 1);
        $this->Cell(12, 6, $model->od_esfera, 1);
        $this->Cell(12, 6, $model->od_cilindro, 1);
        $this->Cell(12, 6, $model->od_eje, 1);
        if ($model->tipo_vision !== 'vision_sencilla') {
            $this->Cell(12, 6, $model->od_add, 1);
            $this->Cell(12, 6, $model->od_altura, 1);
        }
        $this->Cell(12, 6, $model->od_dnp, 1);
        $this->Ln();

        // Filas de la tabla: Ojo izquierdo (OI)
        $this->SetX(1);
        $this->Cell(6, 6, 'OI', 1);
        $this->Cell(12, 6, $model->oi_esfera, 1);
        $this->Cell(12, 6, $model->oi_cilindro, 1);
        $this->Cell(12, 6, $model->oi_eje, 1);
        if ($model->tipo_vision !== 'vision_sencilla') {
            $this->Cell(12, 6, $model->oi_add, 1);
            $this->Cell(12, 6, $model->oi_altura, 1);
        }
        $this->Cell(12, 6, $model->oi_dnp, 1);
        $this->Ln(10);
    }
}

class PDF_AbonoTicket extends FPDF
{
    function __construct()
    {
        parent::__construct('P', 'mm', array(80, 220)); // Ajusta el tamaño según tus necesidades
    }

    function headerTicket($sucursal)
    {
        $pageWidth = $this->GetPageWidth();
        $this->Image('images/optica_logo.jpg', 5, 5, $pageWidth - 10);
        $this->Ln(35);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 6, 'SUCURSAL ' . $sucursal->clave, 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode($sucursal->nombre . "\n" . $sucursal->direccion . "\n" . $sucursal->correo), 0, 'C');
        $this->Ln(5);
    }

    function contactInfoTable($model, $sucursal)
    {
        $this->SetX(10); // Posiciona hacia el centro
        $this->SetFont('Arial', 'B', 10);
        
        // Teléfono
        $this->Cell(30, 4, utf8_decode('TELÉFONO'), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->telefono), 0, 'R'); // Permite que el texto del teléfono se muestre en varias líneas si es necesario

        // Horario
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'HORARIO', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->horario), 0, 'R'); // Permite que el texto del horario se muestre en varias líneas si es necesario
        $this->Ln(3);

        // Nota, Cliente y Fecha
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'NOTA #', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 4, $model->folio, 0, 1, 'R');

        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'CLIENTE', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode($model->nombre_cliente), 0, 'R');

        $this->Ln(3);
        $this->SetFont('Arial', '', 10);
        if ($model->tipo !== 'cotizacion') {
          $this->Cell(0, 4, date('d/m/Y', strtotime($model->fecha_venta)), 0, 1, 'C'); // Centra la fecha
        } else {
          $this->Cell(0, 4, date('d/m/Y', strtotime($model->created)), 0, 1, 'C'); // Centra la fecha
        }        
        $this->Ln(3);
    }

    function contactInfoTableAbono($model, $sucursal, $abono)
    {
        $this->SetX(10); // Posiciona hacia el centro
        $this->SetFont('Arial', 'B', 10);
        
        // Teléfono
        $this->Cell(30, 4, utf8_decode('TELÉFONO'), 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->telefono), 0, 'R'); // Permite que el texto del teléfono se muestre en varias líneas si es necesario

        // Horario
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'HORARIO', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetX(40); // Ajusta la posición para que el texto quede alineado a la derecha
        $this->MultiCell(0, 4, utf8_decode($sucursal->horario), 0, 'R'); // Permite que el texto del horario se muestre en varias líneas si es necesario
        $this->Ln(3);

        // Nota, Cliente y Fecha
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'NOTA #', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(30, 4, $model->folio, 0, 1, 'R');

        $this->SetX(10);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(30, 4, 'CLIENTE', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode($model->nombre_cliente), 0, 'R');

        $this->Ln(3);
        $this->SetFont('Arial', '', 10);
        if ($model->tipo !== 'cotizacion') {
          $this->Cell(0, 4, date('d/m/Y', strtotime($abono->created)), 0, 1, 'C'); // Centra la fecha
        } else {
          $this->Cell(0, 4, date('d/m/Y', strtotime($model->created)), 0, 1, 'C'); // Centra la fecha
        }        
        $this->Ln(3);
    }

    function abonoDetails($cliente, $modelMovimiento, $nuevoSaldo)
    {


        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 5, 'Abono:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, '$' . number_format($modelMovimiento->monto, 2), 0, 1, 'R');
        $this->Ln(5);

        $this->SetFont('Arial', 'B', 10);
        $this->Cell(40, 5, 'Nuevo Saldo:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, '$' . number_format($nuevoSaldo, 2), 0, 1, 'R');
        $this->Ln(10);
    }

    function footerTicket($sucursal)
    {
        $this->SetFont('Arial', '', 10);
        $this->MultiCell(0, 4, utf8_decode("CONSERVA TU TICKET PARA CUALQUIER ACLARACIÓN"), 0, 'C');
        $this->Ln(2);
        $this->MultiCell(0, 4, utf8_decode("DUDAS Y COMENTARIOS: ".$sucursal->telefono_dudas), 0, 'C');
        $this->Ln(2);
        $this->MultiCell(0, 4, utf8_decode("¿SOLICITAS FACTURA? ENVÍA TUS DATOS AL: ".$sucursal->telefono_factura), 0, 'C');
        $this->Ln(2);
    }
}

// ============================================
// AGREGAR ESTA CLASE AL FINAL DEL ARCHIVO
// (después de la clase PDF_AbonoTicket)
// ============================================

class PDF_Receta extends FPDF
{
    private $primaryColor = [41, 128, 185];    // Azul moderno
    private $secondaryColor = [52, 73, 94];    // Gris oscuro
    private $lightGray = [245, 247, 250];      // Fondo suave
    private $borderColor = [189, 195, 199];    // Bordes sutiles

    function __construct() {
        parent::__construct('P', 'mm', 'Letter');
        $this->SetAutoPageBreak(true, 15);
    }

    function recetaCompleta($model, $cliente, $sucursal, $operador)
    {
        $this->SetMargins(12, 12, 12);

        // ========== ENCABEZADO ==========
        $this->headerReceta($sucursal, $model);

        // ========== DATOS DEL PACIENTE ==========
        $this->Ln(4);
        $this->datosPaciente($cliente);

        // ========== AGUDEZA VISUAL ==========
        $this->Ln(5);
        $this->tablaAgudezaVisual($model);

        // ========== IMAGEN DE DIAGNÓSTICOS + CHECKBOXES ==========
        $this->Ln(5);
        $this->seccionDiagnosticosVisual($model);

        // ========== TABLA DE REFRACCIÓN ==========
        $this->Ln(5);
        $this->tablaRefraccion($model);

        // ========== DIAGNÓSTICO Y TRATAMIENTO ==========
        $this->Ln(4);
        $this->seccionDiagnosticoTratamiento($model);

        // ========== PRÓXIMA CITA ==========
        $this->Ln(3);
        $this->seccionProximaCita($model);

        // ========== PIE DE PÁGINA / FIRMA ==========
        $this->Ln(2);
        $this->footerReceta($sucursal, $operador);
    }

    function headerReceta($sucursal, $model)
    {
        // Barra superior con color
        $this->SetFillColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Rect(0, 0, 216, 3, 'F');

        $this->SetY(8);

        // Logo a la izquierda
        if(file_exists('images/optica_logo.jpg')){
            $this->Image('images/optica_logo.jpg', 12, 8, 45);
        }

        // Título a la derecha
        $this->SetXY(100, 10);
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $this->Cell(100, 8, utf8_decode('RECETA ÓPTICA'), 0, 1, 'R');

        // Fecha
        $this->SetXY(100, 20);
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(30, 5, 'Fecha:', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(30, 5, date('d/m/Y', strtotime($model->fecha_venta)), 0, 0, 'L');

        // Folio
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(15, 5, 'Folio:', 0, 0, 'R');
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $folio = $model->folio ?? $model->id;
        if(strlen($folio) > 18) {
            $folio = substr($folio, 0, 18) . '...';
        }
        $this->Cell(25, 5, $folio, 0, 1, 'L');

        // Línea separadora
        $this->SetY(35);
        $this->SetDrawColor($this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);
        $this->Line(12, 35, 204, 35);

        $this->SetTextColor(0, 0, 0);
    }

    function datosPaciente($cliente)
    {
        $this->SetY(39);

        // Título de sección
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(0, 6, 'DATOS DEL PACIENTE', 0, 1, 'L');

        // Caja con fondo
        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->SetDrawColor($this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);

        $y = $this->GetY();
        $this->RoundedRect(12, $y, 192, 12, 2, 'DF');

        $this->SetY($y + 3);
        $this->SetX(16);

        // Nombre
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(18, 4, 'Paciente:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(75, 4, utf8_decode(strtoupper($cliente->nombre ?? '')), 0, 0, 'L');

        // Teléfono
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(15, 4, 'Tel:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(30, 4, $cliente->telefono ?? '', 0, 0, 'L');

        // Empresa
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(18, 4, 'Empresa:', 0, 0, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 4, utf8_decode(strtoupper($cliente->empresa ?? '-')), 0, 1, 'L');

        $this->SetY($y + 14);
        $this->SetTextColor(0, 0, 0);
    }

    function tablaAgudezaVisual($model)
    {
        // Título de sección
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(95, 5, 'AGUDEZA VISUAL', 0, 0, 'L');
        $this->Cell(95, 5, 'OBSERVACIONES', 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);

        $y = $this->GetY();

        // Tabla AV (lado izquierdo)
        $this->SetFillColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(14, 6, 'OJO', 1, 0, 'C', true);
        $this->Cell(25, 6, 'CON LENTES', 1, 0, 'C', true);
        $this->Cell(25, 6, 'SIN LENTES', 1, 0, 'C', true);
        $this->Cell(25, 6, utf8_decode('CORRECCIÓN'), 1, 0, 'C', true);

        // Espacio para observaciones
        $this->SetXY(102, $y);
        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->SetDrawColor($this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);
        $obsY = $y;
        $this->MultiCell(102, 18, '', 1, 'L', true);

        // Filas de datos
        $this->SetY($y + 6);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 9);

        // OD
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->Cell(14, 6, 'OD', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 6, $model->od_av_con_lentes ?? '', 1, 0, 'C');
        $this->Cell(25, 6, $model->od_av_sin_lentes ?? '', 1, 0, 'C');
        $this->Cell(25, 6, $model->od_av_con_correccion ?? '', 1, 1, 'C');

        // OI
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(14, 6, 'OI', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(25, 6, $model->oi_av_con_lentes ?? '', 1, 0, 'C');
        $this->Cell(25, 6, $model->oi_av_sin_lentes ?? '', 1, 0, 'C');
        $this->Cell(25, 6, $model->oi_av_con_correccion ?? '', 1, 1, 'C');

        // Escribir observaciones
        $this->SetXY(104, $obsY + 2);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell(98, 4, utf8_decode($model->observaciones ?? ''), 0, 'L');

        $this->SetY($obsY + 18);
    }

    function seccionDiagnosticosVisual($model)
    {
        // Título de sección
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(0, 5, utf8_decode('DIAGNÓSTICO VISUAL'), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);

        $y = $this->GetY();

        // Imagen de diagnósticos
        $imgPath = 'images/optica_diagnosticos.png';
        $imgWidth = 180;
        $imgX = 18;
        $imgHeight = 36;

        if(file_exists($imgPath)){
            $this->Image($imgPath, $imgX, $y, $imgWidth);
        }

        // Mover cursor DEBAJO de la imagen
        $checkY = $y + $imgHeight + 3;

        // Calcular posiciones para los 3 checkboxes
        $tercio = $imgWidth / 3;

        // Checkbox 1: MIOPÍA
        $x1 = $imgX + ($tercio / 2) - 12;
        $this->checkboxConEtiqueta($x1, $checkY, utf8_decode('MIOPÍA'), $model->tiene_miopia == '1');

        // Checkbox 2: HIPERMETROPÍA
        $x2 = $imgX + $tercio + ($tercio / 2) - 18;
        $this->checkboxConEtiqueta($x2, $checkY, utf8_decode('HIPERMETROPÍA'), $model->tiene_hipermetropia == '1');

        // Checkbox 3: ASTIGMATISMO
        $x3 = $imgX + (2 * $tercio) + ($tercio / 2) - 15;
        $this->checkboxConEtiqueta($x3, $checkY, 'ASTIGMATISMO', $model->tiene_astigmatismo == '1');

        $this->SetY($checkY + 8);
    }

    function checkboxConEtiqueta($x, $y, $label, $checked)
    {
        $this->SetXY($x, $y);

        // Checkbox moderno
        $this->SetDrawColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->SetLineWidth(0.4);

        if($checked){
            $this->SetFillColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
            $this->Rect($x, $y, 5, 5, 'DF');
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 10);
            $this->SetXY($x, $y - 0.5);
            $this->Cell(5, 6, 'X', 0, 0, 'C');
        } else {
            $this->SetFillColor(255, 255, 255);
            $this->Rect($x, $y, 5, 5, 'DF');
        }

        // Etiqueta
        $this->SetTextColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $this->SetFont('Arial', 'B', 9);
        $this->SetXY($x + 7, $y);
        $this->Cell(40, 5, $label, 0, 0, 'L');

        $this->SetLineWidth(0.2);
        $this->SetTextColor(0, 0, 0);
    }

    function tablaRefraccion($model)
    {
        // Título de sección
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(0, 5, utf8_decode('REFRACCIÓN'), 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);

        // Encabezados
        $this->SetFillColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(18, 6, '', 1, 0, 'C', true);
        $this->Cell(28, 6, 'ESFERA', 1, 0, 'C', true);
        $this->Cell(28, 6, 'CILINDRO', 1, 0, 'C', true);
        $this->Cell(22, 6, 'EJE', 1, 0, 'C', true);
        $this->Cell(28, 6, 'ADD', 1, 0, 'C', true);
        $this->Cell(22, 6, 'DNP', 1, 0, 'C', true);
        $this->Cell(28, 6, 'ALTURA', 1, 0, 'C', true);
        $this->Cell(22, 6, 'DIP', 1, 1, 'C', true);

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 9);

        // Fila OD
        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(18, 7, 'OD', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(28, 7, $this->formatearValor($model->od_esfera), 1, 0, 'C');
        $this->Cell(28, 7, $this->formatearValor($model->od_cilindro), 1, 0, 'C');
        $this->Cell(22, 7, $model->od_eje ?? '', 1, 0, 'C');
        $this->Cell(28, 7, $this->formatearValor($model->od_add), 1, 0, 'C');
        $this->Cell(22, 7, $model->od_dnp ?? '', 1, 0, 'C');
        $this->Cell(28, 7, $model->od_altura ?? '', 1, 0, 'C');
        $dipTotal = (floatval($model->od_dnp ?? 0) + floatval($model->oi_dnp ?? 0));
        $this->Cell(22, 7, $dipTotal > 0 ? $dipTotal : '', 1, 1, 'C');

        // Fila OI
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(18, 7, 'OI', 1, 0, 'C', true);
        $this->SetFont('Arial', '', 9);
        $this->Cell(28, 7, $this->formatearValor($model->oi_esfera), 1, 0, 'C');
        $this->Cell(28, 7, $this->formatearValor($model->oi_cilindro), 1, 0, 'C');
        $this->Cell(22, 7, $model->oi_eje ?? '', 1, 0, 'C');
        $this->Cell(28, 7, $this->formatearValor($model->oi_add), 1, 0, 'C');
        $this->Cell(22, 7, $model->oi_dnp ?? '', 1, 0, 'C');
        $this->Cell(28, 7, $model->oi_altura ?? '', 1, 0, 'C');
        $this->Cell(22, 7, '', 1, 1, 'C');
    }

    function formatearValor($valor)
    {
        if(empty($valor) || $valor == '0' || $valor == '0.00'){
            return '';
        }
        $num = floatval($valor);
        if($num > 0){
            return '+' . number_format($num, 2);
        }
        return number_format($num, 2);
    }

    function seccionDiagnosticoTratamiento($model)
    {
        // Dos columnas: Diagnóstico y Tratamiento
        $colWidth = 93;

        // Títulos
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell($colWidth, 5, utf8_decode('DIAGNÓSTICO'), 0, 0, 'L');
        $this->Cell($colWidth, 5, 'TRATAMIENTO', 0, 1, 'L');
        $this->SetTextColor(0, 0, 0);

        $y = $this->GetY();

        // Caja diagnóstico
        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->SetDrawColor($this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);
        $this->RoundedRect(12, $y, $colWidth, 16, 2, 'DF');

        // Caja tratamiento
        $this->RoundedRect(12 + $colWidth + 2, $y, $colWidth, 16, 2, 'DF');

        // Texto diagnóstico
        $this->SetXY(14, $y + 2);
        $this->SetFont('Arial', '', 9);
        $this->MultiCell($colWidth - 4, 4, utf8_decode($model->diagnostico ?? ''), 0, 'L');

        // Texto tratamiento
        $this->SetXY(14 + $colWidth + 2, $y + 2);
        $this->MultiCell($colWidth - 4, 4, utf8_decode($model->tratamiento ?? ''), 0, 'L');

        $this->SetY($y + 18);
    }

    function seccionProximaCita($model)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor($this->primaryColor[0], $this->primaryColor[1], $this->primaryColor[2]);
        $this->Cell(32, 6, utf8_decode('PRÓXIMA CITA:'), 0, 0, 'L');

        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 10);

        $proximaCita = date('d/m/Y', strtotime($model->fecha_venta . ' +1 year'));
        if(!empty($model->proxima_cita)){
            $proximaCita = date('d/m/Y', strtotime($model->proxima_cita));
        }

        $this->SetFillColor($this->lightGray[0], $this->lightGray[1], $this->lightGray[2]);
        $this->SetDrawColor($this->borderColor[0], $this->borderColor[1], $this->borderColor[2]);
        $this->Cell(32, 6, $proximaCita, 1, 1, 'C', true);
    }

    function footerReceta($sucursal, $operador)
    {
        // Espacio antes de la firma
        $this->Ln(4);

        // Imagen de firma (Héctor Hernandez)
        $firmaPath = 'images/optica_firma.jpg';
        if(file_exists($firmaPath)){
            $firmaWidth = 40;
            $firmaX = (216 - $firmaWidth) / 2; // Centrada
            $this->Image($firmaPath, $firmaX, $this->GetY(), $firmaWidth);
            $this->Ln(22); // Espacio para la imagen de firma
        } else {
            $this->Ln(8);
        }

        // Línea para firma centrada
        $this->SetDrawColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $this->Line(80, $this->GetY(), 135, $this->GetY());

        // Espacio entre línea y nombre
        $this->Ln(2);

        // Datos de Héctor Hernandez (fijo para todas las recetas)
        $this->SetFont('Arial', 'B', 8);
        $this->SetTextColor($this->secondaryColor[0], $this->secondaryColor[1], $this->secondaryColor[2]);
        $this->Cell(0, 4, utf8_decode('HÉCTOR HERNÁNDEZ VELÁZQUEZ'), 0, 1, 'C');

        $this->SetFont('Arial', '', 7);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 3, utf8_decode('PROFNAL. TEC. BACH. EN OPTOMETRÍA | Céd. Prof. 7093796'), 0, 1, 'C');

        // Contacto
        $this->SetFont('Arial', '', 6);
        $contacto = $sucursal->nombre . ' | ' . ($sucursal->telefono ?? '') . ' | opticasmiraluz@gmail.com';
        $this->Cell(0, 3, utf8_decode($contacto), 0, 1, 'C');

        $this->SetTextColor(0, 0, 0);
    }

    // Función auxiliar para rectángulos redondeados
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }
}