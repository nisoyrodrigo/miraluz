<?php
class ReporteController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionVentas(){
    $this->render("ventas");
  }

  public function actionGetReporteVentas(){
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

    if(!empty($this->params["bcliente"])){
      $aWhere .= " AND c.nombre LIKE '%".$this->params["bcliente"]."%' ";
    }

    if(!empty($this->params["btelefono"])){
      $aWhere .= " AND c.telefono LIKE '%".$this->params["btelefono"]."%' ";
    }

    if(!empty($this->params["bsucursal"])){
      $aWhere .= " AND v.sucursal = ".$this->params["bsucursal"]." ";
    }

    if(!empty($this->params["boptometrista"])){
      $aWhere .= " AND v.optometrista = ".$this->params["boptometrista"]." ";
    }

    if(!empty($this->params["bvendedor"])){
      $aWhere .= " AND v.user = ".$this->params["bvendedor"]." ";
    }

    if(!empty($this->params["bestatus"])){
      $aWhere .= " AND v.estatus = ".$this->params["bestatus"]." ";
    }

    if (!empty($this->params["bfecha_inicio"])) {
      $aWhere .= " AND DATE(v.fecha_venta) >= '".$this->params["bfecha_inicio"]."' ";
    }

    if (!empty($this->params["bfecha_fin"])) {
      $aWhere .= " AND DATE(v.fecha_venta) <= '".$this->params["bfecha_fin"]."' ";
    }


    $operador = new Operador("WHERE user = ".$this->user->id);
    $rows = Venta::model()->findAll("WHERE tipo = 'venta' AND sucursal IN(".$operador->sucursales.") ".$aWhere);

    $count = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.estatus != 6 AND v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    $rows = Venta::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE  v.estatus != 6 AND v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") ".$aWhere);
    //$rowsquery = Venta::model()->executeQuery("SELECT v.*, c.nombre, c.telefono, GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos_descripcion FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $aWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"]);
    
    $aQuery =  "
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
        WHERE  v.estatus != 6 AND v.tipo = 'venta' 
          AND v.sucursal IN(".$operador->sucursales.") 
        $aWhere
        GROUP BY v.id
        ORDER BY ". $columns[$this->params['order'][0]['column']] ." 
          ".$this->params['order'][0]['dir']."
        LIMIT ".$this->params["start"].", ".$this->params["length"]."
    ";

    $rowsquery = Venta::model()->executeQuery($aQuery);
    //$this->error = $this->params["bcliente"];
    // $this->error = "SELECT v.*, c.nombre, c.telefono FROM ec_venta v LEFT JOIN ec_cliente c ON v.cliente = c.id WHERE v.tipo = 'venta' AND v.sucursal IN(".$operador->sucursales.") $auxWhere $where ORDER BY ". $columns[$this->params['order'][0]['column']]."   ".$this->params['order'][0]['dir']." LIMIT ".$this->params["start"].", ".$this->params["length"];

    $totalsQuery = "
        SELECT 
            SUM(v.subtotal) AS total_subtotal,
            SUM(v.descuento) AS total_descuento,
            SUM(v.total) AS total_total,
            SUM(v.anticipo) AS total_anticipo,
            SUM(v.saldo) AS total_saldo
        FROM ec_venta v
        LEFT JOIN ec_cliente c ON v.cliente = c.id
        WHERE v.estatus != 6 AND v.tipo = 'venta' 
          AND v.sucursal IN(".$operador->sucursales.") 
        $aWhere
    ";

    $totals = Venta::model()->executeQuery($totalsQuery)[0];


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

      $sucursal = new Sucursal($value->sucursal);

      $optometrista = new Operador($value->optometrista);
      $vendedor = new Operador("WHERE user = ".$value->user);
      $rowsquery[$key]->vendedor_nombre = $vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;
      $rowsquery[$key]->optometrista_nombre = $optometrista->nombre." ".$optometrista->apaterno." ".$optometrista->amaterno;
      $rowsquery[$key]->cliente_nombre = $cliente->nombre;
      $rowsquery[$key]->cliente_telefono = $cliente->telefono;
      $rowsquery[$key]->productos_descripcion = $aDetalle;
      $rowsquery[$key]->sucursal_nombre = $sucursal->nombre;
      $rowsquery[$key]->abonos = $value->abonos;

      $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$value->id)[0]->total;
      // $rowsquery[$key]->abonos = $abonos ?? 0;
      $rowsquery[$key]->saldo = $value->saldo - $abonos;
      $estatus = new VentaEstatus($value->estatus);
      $rowsquery[$key]->estatus_descripcion = $estatus->nombre;
    }


    // === TOTAL GLOBAL DE ABONOS (misma lógica que el PDF) ===
    $totalesAbonos = Venta::model()->executeQuery("
        SELECT 
            IFNULL(SUM(vm.monto), 0) AS total_abonos
        FROM ec_venta_movimiento vm
        LEFT JOIN ec_venta v ON vm.venta = v.id
        LEFT JOIN ec_cliente c ON v.cliente = c.id
        WHERE vm.tipo = 'ingreso'
          AND vm.numero != 1
          AND v.tipo = 'venta'
          AND v.estatus != 6
          AND v.sucursal IN(".$operador->sucursales.")
          $aWhere
    ")[0];

    $aSalida["totals"] = [
      "subtotal" => number_format($totals->total_subtotal, 2),
      "descuento" => number_format($totals->total_descuento, 2),
      "total" => number_format($totals->total_total, 2),
      "anticipo" => number_format($totals->total_anticipo, 2),
      "saldo" => number_format($totals->total_saldo, 2),
      "abonos" => number_format($totalesAbonos->total_abonos, 2)
    ];

    $aSalida["draw"] = intval($this->params["draw"]);
    $aSalida["recordsTotal"] = intval($count[0]->total);
    $aSalida["recordsFiltered"] = intval($rows[0]->total);
    $aSalida["data"] = $rowsquery;
    $this->renderJSON($aSalida);
  }


  public function actionGenerarReportePDF() {
    $this->template = null;
    $aWhere = "";
    $filtros = [];

    if (!empty($this->params["bcliente"])) {
        $aWhere .= " AND c.nombre LIKE '%" . $this->params["bcliente"] . "%' ";
        $filtros[] = "Cliente contiene: " . $this->params["bcliente"];
    }
    if (!empty($this->params["btelefono"])) {
        $aWhere .= " AND c.telefono LIKE '%" . $this->params["btelefono"] . "%' ";
        $filtros[] = "Teléfono contiene: " . $this->params["btelefono"];
    }
    if (!empty($this->params["bsucursal"])) {
        $aWhere .= " AND v.sucursal = " . $this->params["bsucursal"] . " ";
        $sucursal = new Sucursal($this->params["bsucursal"]);
        $filtros[] = "Sucursal: " . $sucursal->nombre;
    }
    if (!empty($this->params["bvendedor"])) {
        $vendedor = new Operador("WHERE user = " . $this->params["bvendedor"]);
        $filtros[] = "Vendedor: " . $vendedor->nombre . " " . $vendedor->apaterno;
    }
    if (!empty($this->params["boptometrista"])) {
        $optometrista = new Operador($this->params["boptometrista"]);
        $filtros[] = "Optometrista: " . $optometrista->nombre . " " . $optometrista->apaterno;
    }
    if (!empty($this->params["bestatus"])) {
        $estatus = new VentaEstatus($this->params["bestatus"]);
        $filtros[] = "Estatus: " . $estatus->nombre;
    }
    if (!empty($this->params["bfecha_inicio"])) {
        $aWhere .= " AND DATE(v.fecha_venta) >= '" . $this->params["bfecha_inicio"] . "' ";
        $filtros[] = "Fecha desde: " . date('d/m/Y', strtotime($this->params["bfecha_inicio"]));
    }
    if (!empty($this->params["bfecha_fin"])) {
        $aWhere .= " AND DATE(v.fecha_venta) <= '" . $this->params["bfecha_fin"] . "' ";
        $filtros[] = "Fecha hasta: " . date('d/m/Y', strtotime($this->params["bfecha_fin"]));
    }

    $operador = new Operador("WHERE user = " . $this->user->id);
    $rows = Venta::model()->executeQuery("
        SELECT 
            v.*, 
            c.nombre AS nombre_cliente, 
            c.telefono AS telefono_cliente,
            (
              SELECT IFNULL(SUM(vm.monto), 0) 
              FROM ec_venta_movimiento vm 
              WHERE vm.venta = v.id AND vm.tipo = 'ingreso' AND vm.numero > 1
            ) AS abonos
        FROM ec_venta v
        LEFT JOIN ec_cliente c ON v.cliente = c.id
        WHERE v.estatus != 6 AND v.tipo = 'venta' 
          AND v.sucursal IN(".$operador->sucursales.") 
        $aWhere
        GROUP BY v.id
    ");


    $totalesAbonos = Venta::model()->executeQuery("
      SELECT 
          IFNULL(SUM(vm.monto), 0) AS total_abonos
      FROM ec_venta_movimiento vm
      LEFT JOIN ec_venta v ON vm.venta = v.id
      LEFT JOIN ec_cliente c ON v.cliente = c.id
      WHERE vm.tipo = 'ingreso'
        AND vm.numero != 1
        AND v.tipo = 'venta'
        AND v.estatus != 6
        AND v.sucursal IN(".$operador->sucursales.")
        $aWhere
    ")[0];

    $pdf = new FPDF('P', 'mm', 'Letter');
    $pdf->SetMargins(7.5, 10, 10);
    $pdf->SetAutoPageBreak(true, 20);
    $pdf->AddPage();

    // Título
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, utf8_decode('Reporte de Ventas'), 0, 1, 'C');
    $pdf->Ln(5);

    // Filtros
    $pdf->SetFont('Arial', '', 10);
    if (count($filtros) > 0) {
        foreach ($filtros as $filtro) {
            $pdf->Cell(0, 5, utf8_decode("- ".$filtro), 0, 1);
        }
    } else {
        $pdf->Cell(0, 5, utf8_decode("- Sin filtros aplicados"), 0, 1);
    }
    $pdf->Ln(8);

    // Encabezado de tabla
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(15, 8, 'ID', 1, 0, 'C');
    $pdf->Cell(25, 8, 'Sucursal', 1, 0, 'C');
    $pdf->Cell(60, 8, 'Cliente', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Telefono', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Total', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Anticipo', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Abonos', 1, 0, 'C');
    $pdf->Cell(20, 8, 'Saldo', 1, 1, 'C');

    // Cuerpo de la tabla
    $pdf->SetFont('Arial', '', 8);

    foreach ($rows as $row) {
        $sucursal = new Sucursal($row->sucursal);
        $cliente = $row->nombre_cliente;
        $telefono = $row->telefono_cliente;
        $total = number_format($row->total, 2);
        $abonos = number_format($row->abonos, 2);
        $anticipo = number_format($row->anticipo, 2);
        $saldo = number_format($row->saldo - $abonos, 2);

        $pdf->Cell(15, 8, $row->id, 1, 0, 'C');
        $pdf->Cell(25, 8, utf8_decode($sucursal->nombre), 1, 0);
        $pdf->Cell(60, 8, utf8_decode($cliente), 1, 0);
        $pdf->Cell(20, 8, $telefono, 1, 0);
        $pdf->Cell(20, 8, "$".$total, 1, 0, 'R');
        $pdf->Cell(20, 8, "$".$anticipo, 1, 0, 'R');
        $pdf->Cell(20, 8, "$".$abonos, 1, 0, 'R');
        $pdf->Cell(20, 8, "$".$saldo, 1, 1, 'R');
    }

    // Línea antes de totales
    $pdf->Ln(8);

    $totals = Venta::model()->executeQuery("
        SELECT 
            SUM(v.subtotal) AS total_subtotal,
            SUM(v.descuento) AS total_descuento,
            SUM(v.total) AS total_total,
            SUM(v.anticipo) AS total_anticipo,
            SUM(v.saldo) AS total_saldo
        FROM ec_venta v
        LEFT JOIN ec_cliente c ON v.cliente = c.id
        WHERE v.estatus != 6 AND v.tipo = 'venta' 
          AND v.sucursal IN(".$operador->sucursales.") 
        $aWhere
    ")[0];

    // Totales
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, 'Resumen Totales', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Subtotal: $' . number_format($totals->total_subtotal, 2), 0, 1);
    $pdf->Cell(0, 5, 'Descuento: $' . number_format($totals->total_descuento, 2), 0, 1);
    $pdf->Cell(0, 5, 'Total: $' . number_format($totals->total_total, 2), 0, 1);
    $pdf->Cell(0, 5, 'Anticipo: $' . number_format($totals->total_anticipo, 2), 0, 1);
    $pdf->Cell(0, 5, 'Abonos: $' . number_format($totalesAbonos->total_abonos, 2), 0, 1);
    $pdf->Cell(0, 5, 'Saldo: $' . number_format($totals->total_saldo, 2), 0, 1);

    // Footer
    $pdf->SetY(-15);
    $pdf->SetFont('Arial', 'I', 8);
    $pdf->Cell(0, 10, utf8_decode('Página ').$pdf->PageNo().' - '.date('d/m/Y'), 0, 0, 'C');

    $pdf->Output();
  }


  public function actionGenerarReporteCSV() {
      $this->template = null;
      $this->layout = false;

      // Limpia cualquier output previo
      if (ob_get_length()) ob_clean();

      $aWhere = "";

      // FILTROS
      if (!empty($this->params["bcliente"])) 
          $aWhere .= " AND c.nombre LIKE '%" . $this->params["bcliente"] . "%' ";

      if (!empty($this->params["btelefono"])) 
          $aWhere .= " AND c.telefono LIKE '%" . $this->params["btelefono"] . "%' ";

      if (!empty($this->params["bsucursal"])) 
          $aWhere .= " AND v.sucursal = " . $this->params["bsucursal"] . " ";

      if (!empty($this->params["bestatus"])) 
          $aWhere .= " AND v.estatus = " . $this->params["bestatus"] . " ";

      if (!empty($this->params["bfecha_inicio"])) 
          $aWhere .= " AND DATE(v.fecha_venta) >= '" . $this->params["bfecha_inicio"] . "' ";

      if (!empty($this->params["bfecha_fin"])) 
          $aWhere .= " AND DATE(v.fecha_venta) <= '" . $this->params["bfecha_fin"] . "' ";

      $operador = new Operador("WHERE user = " . $this->user->id);

      $rows = Venta::model()->executeQuery("
          SELECT 
              v.*, 
              c.nombre AS nombre_cliente, 
              c.telefono AS telefono_cliente,
              (
                SELECT IFNULL(SUM(vm.monto), 0) 
                FROM ec_venta_movimiento vm 
                WHERE vm.venta = v.id 
                  AND vm.tipo = 'ingreso' 
                  AND vm.numero > 1
              ) AS abonos
          FROM ec_venta v
          LEFT JOIN ec_cliente c ON v.cliente = c.id
          WHERE v.estatus != 6 
            AND v.tipo = 'venta'
            AND v.sucursal IN(".$operador->sucursales.")
            $aWhere
          GROUP BY v.id
      ");

      // === ENCABEZADOS CSV ===
      $filename = "reporte_ventas_" . date("Y-m-d_His") . ".csv";

      header("Content-Type: text/csv; charset=UTF-8");
      header("Content-Disposition: attachment; filename={$filename}");
      header("Pragma: no-cache");
      header("Expires: 0");

      $output = fopen("php://output", "w");
      fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

      fputcsv($output, [
          'ID',
          'Sucursal',
          'Cliente',
          'Telefono',
          'Total',
          'Anticipo',
          'Abonos',
          'Saldo'
      ]);

      foreach ($rows as $row) {

          $sucursal = Sucursal::model()->findByPk($row->sucursal);

          $total = number_format($row->total, 2);
          $anticipo = number_format($row->anticipo, 2);
          $abonos = number_format($row->abonos, 2);
          $saldoReal = $row->saldo - $row->abonos;

          fputcsv($output, [
              $row->id,
              $sucursal->nombre,
              $row->nombre_cliente,
              $row->telefono_cliente,
              $total,
              $anticipo,
              $abonos,
              number_format($saldoReal, 2)
          ]);
      }

      fclose($output);
      exit;
  }





}