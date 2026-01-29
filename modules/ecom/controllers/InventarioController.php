<?php
class InventarioController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionTransferencia(){
    $this->render("transferencia");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Compra($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionEditElementTransferencia(){
    $this->template = null;
    $data = new AlmacenTransferencia($this->params["id"]);
    $this->render("edit-transferencia", array("data" => $data));
  }

  public function actionDetalleTransferencia(){
    $data = new AlmacenTransferencia($this->params["id"]);
    $this->render("detalle-transferencia", array("data" => $data));
  }

  public function actionAgregarProducto(){
    $this->template = null;
    $data = new AlmacenTransferencia($this->params["transferencia"]);
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
      0 => 'id',
      1 => 'nombre',
      2 => 'familia',
      3 => 'almacen5',
      4 => 'almacen1',
      5 => 'almacen3',
      6 => 'almacen2',
      7 => 'almacen4',
    );

    $aWhere = "";
    if (!empty($this->params['search']['value'])) {
      $search = $this->params['search']['value'];
      $aWhere .= " AND (p.id LIKE '%$search%' OR p.nombre LIKE '%$search%') ";
    }

    $count = Producto::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_producto p WHERE 1 = 1 ".$aWhere);

    $rowsquery = Producto::model()->executeQuery("
        SELECT * FROM (
            SELECT 
                p.id,
                p.nombre,
                p.familia,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 1 AND producto = p.id) AS almacen1,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 2 AND producto = p.id) AS almacen2,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 3 AND producto = p.id) AS almacen3,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 4 AND producto = p.id) AS almacen4,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 5 AND producto = p.id) AS almacen5
            FROM ec_producto p
            WHERE 1 = 1 $aWhere
        ) AS subquery
        ORDER BY ". $columns[$this->params['order'][0]['column']] ." ".$this->params['order'][0]['dir']."
        LIMIT ".$this->params["start"].", ".$this->params["length"]."
    ");

    foreach ($rowsquery as $key => $value) {
      $familia = $value->familia;
      if($familia == "armazon"){
        $familia = "producto";
      }
      $rowsquery[$key]->familia = $familia;
    }
      
    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($count[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);

  }

  public function actionGetAllTransferencias(){
    $this->template = null;

    $aSalida = array();

    $columns = array( 
        0 => 't.id',
        1 => 'a_origen.nombre',   // Nombre del almacén de origen
        2 => 'a_destino.nombre',  // Nombre del almacén de destino
        3 => 'envia',             // Nombre del operador que envía
        4 => 'recibe',            // Nombre del operador que recibe
        5 => 't.referencia',      // Referencia
        6 => 't.estatus',         // Estatus de la transferencia
    );

    $aWhere = "WHERE 1 = 1";
    if (!empty($this->params['search']['value'])) {
        $search = $this->params['search']['value'];
        $aWhere .= " AND (t.id LIKE '%$search%' 
                          OR a_origen.nombre LIKE '%$search%' 
                          OR a_destino.nombre LIKE '%$search%' 
                          OR CONCAT(o_envia.nombre, ' ', o_envia.apaterno, ' ', o_envia.amaterno) LIKE '%$search%'
                          OR CONCAT(o_recibe.nombre, ' ', o_recibe.apaterno, ' ', o_recibe.amaterno) LIKE '%$search%' 
                          OR t.referencia LIKE '%$search%')";
    }

    // Contar registros totales
    $count = Producto::model()->executeQuery("
        SELECT COUNT(*) AS total 
        FROM ec_almacen_transferencia t
        LEFT JOIN ec_almacen a_origen ON t.almacen_origen = a_origen.id
        LEFT JOIN ec_almacen a_destino ON t.almacen_destino = a_destino.id
        LEFT JOIN ec_operador o_envia ON t.user_envia = o_envia.user
        LEFT JOIN ec_operador o_recibe ON t.user_recibe = o_recibe.user
        $aWhere
    ");

    // Obtener registros con orden y límite
    $rowsquery = Producto::model()->executeQuery("
        SELECT 
            t.id,
            a_origen.nombre AS origen,
            a_destino.nombre AS destino,
            CONCAT(o_envia.nombre, ' ', o_envia.apaterno, ' ', o_envia.amaterno) AS envia,
            CONCAT(o_recibe.nombre, ' ', o_recibe.apaterno, ' ', o_recibe.amaterno) AS recibe,
            t.referencia,
            t.estatus
        FROM ec_almacen_transferencia t
        LEFT JOIN ec_almacen a_origen ON t.almacen_origen = a_origen.id
        LEFT JOIN ec_almacen a_destino ON t.almacen_destino = a_destino.id
        LEFT JOIN ec_operador o_envia ON t.user_envia = o_envia.user
        LEFT JOIN ec_operador o_recibe ON t.user_recibe = o_recibe.user
        $aWhere
        ORDER BY ". $columns[$this->params['order'][0]['column']] ." ".$this->params['order'][0]['dir']."
        LIMIT ".$this->params["start"].", ".$this->params["length"]."
    ");

    foreach ($rowsquery as $key => $value) {
      $familia = $value->familia;
      if($familia == "armazon"){
        $familia = "producto";
      }
      $rowsquery[$key]->familia = $familia;
    }
      
    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($count[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);

  }

  public function actionGetAllDetalle(){
    $this->template = null;
    $rows = AlmacenTransferenciaDetalle::model()->findAll("WHERE transferencia = ".$this->params["id"]);
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


  public function actionSaveTransferencia(){
    $model = new AlmacenTransferencia($this->params["id"]);
    $model->setAttributes($this->params);

    if($model->id == ""){
      $model->user_envia = $this->user->id;
    }

    if($this->params["almacen_origen"] == $this->params["almacen_destino"]){
      $this->error .= "No puedes transferir al mismo almacén";
    }

    if($this->error == ""){
      if(!$model->save()){
        $this->error = $model->error;
      } else {
        $dataModel = $model->getAttributes();
        $model = new AlmacenTransferencia($dataModel->id);
      } 

      if(!$model->save()){
        $this->error = "Error al actualizar transferencia".$model->error;
      }
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
  
  public function actionDestroyTransferenciaProducto(){
    $this->template = null;
    $model = new AlmacenTransferenciaDetalle($this->params["id"]);
    $transferencia = new AlmacenTransferencia($model->transferencia);
    if($transferencia->estatus != "pendiente"){
      $this->error .= "No puedes eliminar un producto, la transferencia ya fue enviada.";
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
    $model = new AlmacenTransferenciaDetalle("WHERE transferencia = ".$this->params["compra"]." AND producto = ".$this->params["id"]);
    if($model->id != ""){
      $model->cantidad = $this->params["cantidad"];
    } else {
      $model->transferencia = $this->params["compra"];
      $model->producto = $this->params["id"];
      $model->cantidad = $this->params["cantidad"];
    }

    if(!$model->save()){
      $this->error = $model->error;
    }
    $this->renderJSON($model->getAttributes());
  }

  public function actionTerminaTransferencia(){
    $this->template = null;
    $model = new AlmacenTransferencia($this->params["id"]);
    $almacen = new Almacen($model->almacen_origen);
    $detalle = AlmacenTransferenciaDetalle::model()->findAll("WHERE transferencia = ".$model->id);
    if($model->estatus != "pendiente"){
      $this->error .= "La transferencia ya fue enviada y/o completada.";
    }

    if(count($detalle) == 0){
      $this->error .= "No puedes enviar una transferencia sin productos.";
    }

    if($this->error == ""){
      $model->estatus = "enviada";
      if(!$model->save()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON();
  }

  public function actionCancelaTransferencia(){
    $this->template = null;
    $model = new AlmacenTransferencia($this->params["id"]);
    $almacen = new Almacen($model->almacen_origen);
    $detalle = AlmacenTransferenciaDetalle::model()->findAll("WHERE transferencia = ".$model->id);
    if($model->estatus == "recibida"){
      $this->error .= "La transferencia ya fue recibida.";
    }

    if($this->error == ""){
      $model->estatus = "cancelada";
      $model->user_recibe = $this->user->id;
      if(!$model->save()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON();
  }

  public function actionRecibeTransferencia(){
    $this->template = null;
    $model = new AlmacenTransferencia($this->params["id"]);
    $almacen = new Almacen($model->almacen_origen);
    $almacenDestino = new Almacen($model->almacen_destino);
    $detalle = AlmacenTransferenciaDetalle::model()->findAll("WHERE transferencia = ".$model->id);
    if($model->estatus != "enviada"){
      $this->error .= "La transferencia no ha sido enviada.";
    }

    if($this->error == ""){
      foreach ($detalle as $key => $value) {
        $almacenMovimiento = new AlmacenMovimiento();
        $almacenMovimiento->almacen = $almacen->id;
        $almacenMovimiento->producto = $value->producto;
        $almacenMovimiento->tipo = "transferencia";
        $almacenMovimiento->cantidad = $value->cantidad;
        $almacenMovimiento->referencia = $model->id;
        $almacenMovimiento->save();


        $almacenMovimientoE = new AlmacenMovimiento();
        $almacenMovimientoE->almacen = $almacenDestino->id;
        $almacenMovimientoE->producto = $value->producto;
        $almacenMovimientoE->tipo = "transferencia";
        $almacenMovimientoE->cantidad = $value->cantidad;
        $almacenMovimientoE->referencia = $model->id;
        $almacenMovimientoE->save();

        $inventarioPrevio = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacen->id);
        if($inventarioPrevio->id != ""){
          $inventarioPrevio->cantidad_actual = $inventarioPrevio->cantidad_actual - $value->cantidad;
          $inventarioPrevio->save();
        }

        $inventarioNuevo = new Inventario("WHERE producto = ".$value->producto." AND almacen = ".$almacenDestino->id);
        if($inventarioNuevo->id != ""){
          $inventarioNuevo->cantidad_actual = $inventarioNuevo->cantidad_actual + $value->cantidad;
          $inventarioNuevo->save();
        }

      }
    }

    if($this->error == ""){
      $model->estatus = "recibida";
      $model->user_recibe = $this->user->id;
      if(!$model->save()){
        $this->error = $model->error;
      }
    }
    $this->renderJSON();
  }



  public function actionGetAllProductos(){
      $template = null;
      $aWhere = "";
      $almacenId = intval($this->params["id"]);

      // Filtrar productos disponibles en inventario
      $rows = Producto::model()->executeQuery("
          SELECT 
              p.*, 
              i.cantidad_actual 
          FROM ec_producto p
          INNER JOIN ec_inventario i ON p.id = i.producto
          WHERE p.familia = 'armazon' AND p.estatus = 1 
            AND i.almacen = $almacenId 
            AND i.cantidad_actual > 0
      ");

      foreach ($rows as $key => $value) {
          $tipo = new TipoProducto($value->tipo);
          $marca = new MarcaProducto($value->tipo);
          $rows[$key]->tipo_nombre = $tipo->nombre;
          $rows[$key]->marca_nombre = $marca->nombre;

          // Limitar la cantidad máxima permitida
          $rows[$key]->cantidad_maxima = intval($value->cantidad_actual);
      }

      $this->renderJSON($rows);
  }

  public function actionGetAllMicas(){
      $template = null;
      $aWhere = "";
      $almacenId = intval($this->params["id"]);

      // Filtrar productos disponibles en inventario
      $rows = Producto::model()->executeQuery("
          SELECT 
              p.*, 
              i.cantidad_actual 
          FROM ec_producto p
          INNER JOIN ec_inventario i ON p.id = i.producto
          WHERE p.familia = 'mica' AND p.estatus = 1 
            AND i.almacen = $almacenId 
            AND i.cantidad_actual > 0
      ");

      foreach ($rows as $key => $value) {
          $tipo = new TipoProducto($value->tipo);
          $rows[$key]->tipo_nombre = $tipo->nombre;

          // Limitar la cantidad máxima permitida
          $rows[$key]->cantidad_maxima = intval($value->cantidad_actual);
      }

      $this->renderJSON($rows);
  }

  public function actionGetAllExtras(){
      $template = null;
      $aWhere = "";
      $almacenId = intval($this->params["id"]);

      // Filtrar productos disponibles en inventario
      $rows = Producto::model()->executeQuery("
          SELECT 
              p.*, 
              i.cantidad_actual 
          FROM ec_producto p
          INNER JOIN ec_inventario i ON p.id = i.producto
          WHERE p.familia = 'extra' AND p.estatus = 1 
            AND i.almacen = $almacenId 
            AND i.cantidad_actual > 0
      ");

      foreach ($rows as $key => $value) {
          $tipo = new TipoProducto($value->tipo);
          $rows[$key]->tipo_nombre = $tipo->nombre;

          // Limitar la cantidad máxima permitida
          $rows[$key]->cantidad_maxima = intval($value->cantidad_actual);
      }

      $this->renderJSON($rows);
  }

public function actionGenerarPDFInventario() {
    $this->template = null;

    // Obtener datos de la sucursal principal
    $sucursal = new Sucursal(1);

    // Datos del inventario
    $rowsquery = Producto::model()->executeQuery(
        "SELECT * FROM (
            SELECT 
                p.id,
                p.nombre,
                p.familia,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 1 AND producto = p.id) AS almacen1,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 2 AND producto = p.id) AS almacen2,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 3 AND producto = p.id) AS almacen3,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 4 AND producto = p.id) AS almacen4,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 5 AND producto = p.id) AS almacen5
            FROM ec_producto p
            WHERE 1 = 1
        ) AS subquery"
    );

    // Crear el PDF
    $pdf = new FPDF('L', 'mm', 'A4'); // Cambiar orientación a horizontal (Landscape)
    $pdf->AddPage();

    // Título y logo
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, utf8_decode('Inventario Óptica Miraluz'), 0, 1, 'C');
    $pdf->Image('images/optica_logo.jpg', 10, 10, 30); // Logo de la sucursal
    $pdf->Ln(20);

    // Agregar encabezados de la tabla
    function agregarEncabezados($pdf) {
        $pdf->SetFillColor(200, 220, 255); // Color bonito para los encabezados
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
        $pdf->Cell(80, 10, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Familia', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Principal', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Macroplaza', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Ecatepec', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Flores', 1, 0, 'C', true);
        $pdf->Cell(25, 10, 'Tizayuca', 1, 0, 'C', true);
        $pdf->Cell(30, 10, 'Total', 1, 1, 'C', true);
    }

    // Agregar encabezados y contenido desde la primera página
    agregarEncabezados($pdf);

    // Contenido de la tabla
    foreach ($rowsquery as $row) {
        if ($pdf->GetY() > 180) { // Salto de página si es necesario
            $pdf->AddPage();
            agregarEncabezados($pdf);
        }

        $total = $row->almacen1 + $row->almacen2 + $row->almacen3 + $row->almacen4 + $row->almacen5;
        
        // Calcular la altura dinámica del nombre para igualar el resto de las celdas
        $pdf->SetFont('Arial', '', 9);
        $nombreWidth = 80; // Ancho de la celda del nombre
        $nombreLines = ceil($pdf->GetStringWidth(utf8_decode($row->nombre)) / $nombreWidth);
        $rowHeight = max(10, $nombreLines * 5); // Altura mínima de 10, ajustada según el contenido

        $rowHeight = 10;

        // Ajustar las celdas para evitar desbordes o recortes
        $pdf->Cell(15, $rowHeight, $row->id, 1, 0, 'C');

        // Nombre en multilinea con altura uniforme
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(80, $rowHeight, utf8_decode($row->nombre), 1, 0, 'L');
        $pdf->Cell(25, $rowHeight, utf8_decode($row->familia), 1, 0, 'C');
        $pdf->Cell(25, $rowHeight, $row->almacen1, 1, 0, 'C');
        $pdf->Cell(25, $rowHeight, $row->almacen2, 1, 0, 'C');
        $pdf->Cell(25, $rowHeight, $row->almacen3, 1, 0, 'C');
        $pdf->Cell(25, $rowHeight, $row->almacen4, 1, 0, 'C');
        $pdf->Cell(25, $rowHeight, $row->almacen5, 1, 0, 'C');
        $pdf->Cell(30, $rowHeight, $total, 1, 1, 'C');
    }

    // Renderizar el PDF en el navegador
    $pdf->Output('I'); // 'I' para mostrar en el navegador
  }

  public function actionExportaCSV() {
    $this->template = null;

    // Obtener datos de la sucursal principal
    $sucursal = new Sucursal(1);

    // Datos del inventario
    $rowsquery = Producto::model()->executeQuery(
        "SELECT * FROM (
            SELECT 
                p.id,
                p.nombre,
                p.familia,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 1 AND producto = p.id) AS almacen1,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 2 AND producto = p.id) AS almacen2,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 3 AND producto = p.id) AS almacen3,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 4 AND producto = p.id) AS almacen4,
                (SELECT cantidad_actual FROM ec_inventario WHERE almacen = 5 AND producto = p.id) AS almacen5
            FROM ec_producto p
            WHERE 1 = 1
        ) AS subquery"
    );

    // Configurar encabezados para la exportación CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="inventario.csv"');
    echo "\xEF\xBB\xBF"; // Para codificación UTF-8

    // Crear el archivo CSV
    $output = fopen('php://output', 'w');

    // Escribir encabezados en el CSV
    fputcsv($output, ['ID', 'Nombre', 'Familia', 'Principal', 'Macroplaza', 'Ecatepec', 'Flores', 'Tizayuca', 'Total']);

    // Escribir contenido
    foreach ($rowsquery as $row) {
        $total = $row->almacen1 + $row->almacen2 + $row->almacen3 + $row->almacen4 + $row->almacen5;
        fputcsv($output, [
            $row->id,
            $row->nombre,
            $row->familia,
            $row->almacen1,
            $row->almacen2,
            $row->almacen3,
            $row->almacen4,
            $row->almacen5,
            $total
        ]);
    }

    // Cerrar la salida
    fclose($output);

    // Terminar la ejecución del script para evitar salida adicional
    exit();
  }








}