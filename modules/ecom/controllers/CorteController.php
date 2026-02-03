<?php

class CorteController extends Controller{

  public function actionInit(){
    $this->render("index");
  }

  public function actionEditElement(){
    $this->template = null;
    $data = new Corte($this->params["id"]);
    $this->render("edit", array("data" => $data));
  }

  public function actionDetalleCorte(){
    $this->template = null;
    $data = new Corte($this->params["id"]);
    $this->render("detalle", array("data" => $data));
  }
  
  public function actionGetAll(){
    $template = null;
    $aWhere = "";
    $rows = Corte::model()->findAll("WHERE estatus IN (1,2) ".$aWhere);
    foreach ($rows as $key => $value) {
      $sucursal = new Sucursal($value->sucursal);
      $usuario = new Operador("WHERE user = ".$this->user->id);

      $rows[$key]->fecha = date('d/m/Y', strtotime($value->fecha));
      $rows[$key]->nombre_sucursal = $sucursal->nombre;
      $rows[$key]->nombre_usuario = $usuario->nombre." ".$usuario->apaterno." ".$usuario->amaterno;
    }
    $this->renderJSON($rows);
  }


  public function actionSave(){
    $this->template = null;

    // -----------------------------
    // 0) Validaciones básicas
    // -----------------------------
    $sucursalId = intval($this->params["sucursal"] ?? 0);
    if($sucursalId <= 0){
      $this->error = "Sucursal es obligatoria.";
      return $this->renderJSON();
    }

    $fecha = !empty($this->params["fecha"]) ? $this->params["fecha"] : date('Y-m-d');

    // Rangos del día (evita DATE() en índice)
    $inicio = $fecha . " 00:00:00";
    $fin    = date('Y-m-d 00:00:00', strtotime($fecha . ' +1 day'));

    // -----------------------------
    // 1) No debe existir otro corte (misma sucursal + fecha)
    // -----------------------------
    // Ajusta si tu columna no se llama "fecha"
    $existe = Corte::model()->executeQuery("
      SELECT id
      FROM ec_corte
      WHERE sucursal = $sucursalId
        AND fecha = '$fecha'
        AND estatus IN(1,2)
      LIMIT 1
    ");

    if(!empty($existe)){
      $this->error = "Ya existe un corte para esa sucursal y fecha (corte #".$existe[0]->id.").";
      return $this->renderJSON();
    }

    // -----------------------------
    // 2) No crear nada si no hay movimientos
    //    (ingresos del día, ventas no canceladas)
    // -----------------------------
    $hay = VentaMovimiento::model()->executeQuery("
      SELECT COUNT(*) AS total
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE vm.tipo = 'ingreso'
        AND vm.corte_id IS NULL
        AND v.sucursal = $sucursalId
        AND v.estatus != 6
        AND vm.created >= '$inicio'
        AND vm.created <  '$fin'
    ")[0]->total ?? 0;

    if(intval($hay) <= 0){
      $this->error = "No hay movimientos de ingreso para generar corte en esa fecha.";
      return $this->renderJSON();
    }

    // -----------------------------
    // 3) Transacción
    // -----------------------------
    Corte::model()->executeQuery("START TRANSACTION");

    try {

      // 3.1) Obtener fondo de caja del día anterior (último corte cerrado de la sucursal)
      $fondo = 0.0;
      $topeCaja = 1100.0;

      $corteAnterior = Corte::model()->executeQuery("
        SELECT fondo_caja, efectivo_ingreso
        FROM ec_corte
        WHERE sucursal = $sucursalId
          AND fecha < '$fecha'
          AND estatus = 2
        ORDER BY fecha DESC
        LIMIT 1
      ");

      if(!empty($corteAnterior)){
        $fondoAnterior = floatval($corteAnterior[0]->fondo_caja ?? 0);
        $efectivoAnterior = floatval($corteAnterior[0]->efectivo_ingreso ?? 0);
        // Lo que quedó para el día siguiente del corte anterior
        $fondo = min($topeCaja, $fondoAnterior + $efectivoAnterior);
      }

      // 3.2) Crear corte base
      $corte = new Corte();
      $corte->setAttributes($this->params);
      $corte->user = $this->user->id;
      $corte->sucursal = $sucursalId;
      $corte->fecha = $fecha;
      $corte->fondo_caja = $fondo;

      // si tienes default estatus en DB, puedes omitirlo
      $corte->estatus = 1;

      if(!$corte->save()){
        throw new Exception($corte->error ?: "No se pudo guardar el corte.");
      }

      // 3.3) Amarrar movimientos del día al corte
      // (solo ingresos, ventas no canceladas, misma sucursal, dentro del rango)
      VentaMovimiento::model()->executeQuery("
        UPDATE ec_venta_movimiento vm
        INNER JOIN ec_venta v ON v.id = vm.venta
        SET vm.corte_id = ".$corte->id."
        WHERE vm.tipo = 'ingreso'
          AND vm.corte_id IS NULL
          AND v.sucursal = $sucursalId
          AND v.estatus != 6
          AND vm.created >= '$inicio'
          AND vm.created <  '$fin'
      ");

      // 3.4) Recalcular totales YA amarrados (snapshot)
      $tot = VentaMovimiento::model()->executeQuery("
        SELECT vm.forma_pago, IFNULL(SUM(vm.monto),0) AS total
        FROM ec_venta_movimiento vm
        WHERE vm.tipo='ingreso'
          AND vm.corte_id = ".$corte->id."
        GROUP BY vm.forma_pago
      ");

      $map = ["efectivo"=>0, "tarjeta"=>0, "tarjetac"=>0, "vales"=>0];
      foreach($tot as $r){
        if(isset($map[$r->forma_pago])) $map[$r->forma_pago] = floatval($r->total);
      }

      $corte->efectivo_ingreso = $map["efectivo"];
      $corte->tarjeta_ingreso  = $map["tarjeta"];
      $corte->tarjetac_ingreso = $map["tarjetac"];
      $corte->vales_ingreso    = $map["vales"];

      // 3.5) Contados (si no vienen, 0)
      $corte->efectivo_contado = floatval($this->params["efectivo_contado"] ?? 0);
      $corte->tarjeta_contado  = floatval($this->params["tarjeta_contado"] ?? 0);
      $corte->tarjetac_contado = floatval($this->params["tarjetac_contado"] ?? 0);
      $corte->vales_contado    = floatval($this->params["vales_contado"] ?? 0);

      // 3.6) Depósito sugerido (tu regla)
      $efectivoDisponible = $fondo + $corte->efectivo_ingreso;
      $corte->deposito = max(0, $efectivoDisponible - $topeCaja);

      // 3.7) Cerrar
      $corte->estatus = 2;
      $corte->cerrado_at = date('Y-m-d H:i:s');

      if(!$corte->save()){
        throw new Exception($corte->error ?: "No se pudo cerrar el corte.");
      }

      Corte::model()->executeQuery("COMMIT");

      return $this->renderJSON([
        "corte" => $corte->getAttributes(),
        "totales" => $map,
        "movs" => intval($hay),
      ]);

    } catch (Throwable $e) {
      Corte::model()->executeQuery("ROLLBACK");
      $this->error = $e->getMessage();
      return $this->renderJSON();
    }
  }





  public function actionGetResumen(){
    $this->template = null;

    $sucursal = intval($this->params["sucursal"] ?? 0);
    $fecha = $this->params["fecha"] ?? "";

    if(!$sucursal || $fecha == ""){
      $this->error = "Sucursal y fecha son obligatorios.";
      return $this->renderJSON();
    }

    // Totales por forma pago (solo ingresos, y ventas no canceladas)
    $totales = VentaMovimiento::model()->executeQuery("
      SELECT vm.forma_pago, IFNULL(SUM(vm.monto),0) AS total
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE v.estatus != 6
        AND v.sucursal = $sucursal
        AND vm.tipo = 'ingreso'
        AND DATE(vm.created) = '$fecha'
      GROUP BY vm.forma_pago
    ");

    $map = [
      "efectivo" => 0,
      "tarjeta" => 0,
      "tarjetac" => 0,
      "vales" => 0,
    ];
    foreach($totales as $r){
      $fp = $r->forma_pago;
      if(isset($map[$fp])) $map[$fp] = floatval($r->total);
    }

    // Desglose de tarjetas: banco + últimos 4 + cantidad de pagos + total
    $tarjetas = VentaMovimiento::model()->executeQuery("
      SELECT
        vm.forma_pago,
        vm.banco,
        vm.tarjeta_digitos,
        IFNULL(SUM(vm.monto),0) AS total
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE v.estatus != 6
        AND v.sucursal = $sucursal
        AND vm.tipo = 'ingreso'
        AND vm.forma_pago IN('tarjeta','tarjetac')
        AND DATE(vm.created) = '$fecha'
      GROUP BY vm.forma_pago, vm.banco, vm.tarjeta_digitos
      ORDER BY vm.banco, vm.tarjeta_digitos
    ");

    // Obtener fondo de caja del día anterior (último corte cerrado de la sucursal)
    $fondo = 0.0;
    $topeCaja = 1100.0;

    $corteAnterior = Corte::model()->executeQuery("
      SELECT fondo_caja, efectivo_ingreso
      FROM ec_corte
      WHERE sucursal = $sucursal
        AND fecha < '$fecha'
        AND estatus = 2
      ORDER BY fecha DESC
      LIMIT 1
    ");

    if(!empty($corteAnterior)){
      $fondoAnterior = floatval($corteAnterior[0]->fondo_caja ?? 0);
      $efectivoAnterior = floatval($corteAnterior[0]->efectivo_ingreso ?? 0);
      // Lo que quedó para el día siguiente del corte anterior
      $fondo = min($topeCaja, $fondoAnterior + $efectivoAnterior);
    }

    $efectivoDisponible = $fondo + $map["efectivo"];
    $dejarCaja = $topeCaja;
    $depositoSugerido = max(0, $efectivoDisponible - $dejarCaja);
    $efectivoParaManana = min($dejarCaja, $efectivoDisponible);

    // Pendientes para enviar a laboratorio (>=30% pagado y aún en "apartado" 7)
    // Aquí calculo pagado sumando movimientos tipo ingreso (anticipo + abonos)
    $pendientesLab = Venta::model()->executeQuery("
      SELECT
        v.id,
        v.folio,
        v.total,
        v.estatus,
        DATE_FORMAT(v.fecha_venta, '%d/%m/%Y') AS fecha_venta,
        c.nombre AS cliente_nombre,
        IFNULL(SUM(vm.monto),0) AS pagado
      FROM ec_venta v
      LEFT JOIN ec_cliente c ON c.id = v.cliente
      LEFT JOIN ec_venta_movimiento vm ON vm.venta = v.id AND vm.tipo = 'ingreso'
      WHERE v.tipo = 'venta'
        AND v.sucursal = $sucursal
        AND v.estatus = 8
      GROUP BY v.id
      ORDER BY v.fecha_venta DESC
    ");

    $totalDia = floatval($map["efectivo"] + $map["tarjeta"] + $map["tarjetac"] + $map["vales"]);


    $salida = [
      "fecha" => $fecha,
      "sucursal" => $sucursal,
      "totales" => [
        "efectivo" => $map["efectivo"],
        "tarjeta" => $map["tarjeta"],
        "tarjetac" => $map["tarjetac"],
        "vales" => $map["vales"],
        "total" => $totalDia,
      ],
      "deposito" => [
        "fondo_caja" => $fondo,
        "efectivo_disponible" => $efectivoDisponible,
        "efectivo_para_manana" => $efectivoParaManana,
        "deposito_sugerido" => $depositoSugerido,
        "tope_caja" => $dejarCaja,
      ],
      "tarjetas" => $tarjetas,
      "pendientes_laboratorio" => $pendientesLab,
    ];

    $this->renderJSON($salida);
  }

  
  public function actionDestroy(){
    $model = new Corte($this->params["id"]);
    $model->estatus = 2;
    if(!$model->save()){
      $this->error = $model->error;
    }

    $this->renderJSON($model->getAttributes());
  }


  public function actionRecalcular(){
    $this->template = null;

    $corteId = intval($this->params["id"] ?? 0);
    if($corteId <= 0){
      $this->error = "Falta id de corte.";
      return $this->renderJSON();
    }

    $corte = new Corte($corteId);
    if(empty($corte->id)){
      $this->error = "No existe el corte.";
      return $this->renderJSON();
    }

    $sucursalId = intval($corte->sucursal);
    $fecha = $corte->fecha;

    // Rangos del día
    $inicio = $fecha . " 00:00:00";
    $fin = date('Y-m-d 00:00:00', strtotime($fecha . ' +1 day'));

    // Contar movimientos pendientes (sin corte asignado) del mismo día y sucursal
    $pendientes = VentaMovimiento::model()->executeQuery("
      SELECT COUNT(*) AS total
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE vm.tipo = 'ingreso'
        AND vm.corte_id IS NULL
        AND v.sucursal = $sucursalId
        AND v.estatus != 6
        AND vm.created >= '$inicio'
        AND vm.created < '$fin'
    ")[0]->total ?? 0;

    if(intval($pendientes) <= 0){
      $this->error = "No hay movimientos nuevos para agregar al corte.";
      return $this->renderJSON();
    }

    // Transacción
    Corte::model()->executeQuery("START TRANSACTION");

    try {
      // 1) Asignar movimientos pendientes al corte
      VentaMovimiento::model()->executeQuery("
        UPDATE ec_venta_movimiento vm
        INNER JOIN ec_venta v ON v.id = vm.venta
        SET vm.corte_id = $corteId
        WHERE vm.tipo = 'ingreso'
          AND vm.corte_id IS NULL
          AND v.sucursal = $sucursalId
          AND v.estatus != 6
          AND vm.created >= '$inicio'
          AND vm.created < '$fin'
      ");

      // 2) Recalcular totales (todos los movimientos del corte)
      $tot = VentaMovimiento::model()->executeQuery("
        SELECT vm.forma_pago, IFNULL(SUM(vm.monto),0) AS total
        FROM ec_venta_movimiento vm
        INNER JOIN ec_venta v ON v.id = vm.venta
        WHERE vm.tipo = 'ingreso'
          AND vm.corte_id = $corteId
          AND v.estatus != 6
        GROUP BY vm.forma_pago
      ");

      $map = ["efectivo"=>0, "tarjeta"=>0, "tarjetac"=>0, "vales"=>0];
      foreach($tot as $r){
        if(isset($map[$r->forma_pago])) $map[$r->forma_pago] = floatval($r->total);
      }

      $corte->efectivo_ingreso = $map["efectivo"];
      $corte->tarjeta_ingreso  = $map["tarjeta"];
      $corte->tarjetac_ingreso = $map["tarjetac"];
      $corte->vales_ingreso    = $map["vales"];

      // 3) Recalcular depósito sugerido (fondo_caja no se toca)
      $fondo = floatval($corte->fondo_caja ?? 0);
      $topeCaja = 1100.0;
      $efectivoDisponible = $fondo + $corte->efectivo_ingreso;
      $corte->deposito = max(0, $efectivoDisponible - $topeCaja);

      // 4) Actualizar timestamp de cierre
      $corte->cerrado_at = date('Y-m-d H:i:s');

      if(!$corte->save()){
        throw new Exception($corte->error ?: "No se pudo actualizar el corte.");
      }

      Corte::model()->executeQuery("COMMIT");

      return $this->renderJSON([
        "corte" => $corte->getAttributes(),
        "totales" => $map,
        "movimientos_agregados" => intval($pendientes),
      ]);

    } catch (Throwable $e) {
      Corte::model()->executeQuery("ROLLBACK");
      $this->error = $e->getMessage();
      return $this->renderJSON();
    }
  }


  public function actionImprimeCorte(){
    $this->template = null;

    $corteId = intval($this->params["id"] ?? 0);
    if($corteId <= 0){
      die("Falta id de corte");
    }

    $corte = new Corte($corteId);
    if(empty($corte->id)){
      die("No existe el corte");
    }

    $sucursal = new Sucursal($corte->sucursal);
    $operador = new Operador("WHERE user = ".$corte->user);

    // 1) Movimientos detallados (TODOS), con hora/folio/banco/dígitos
    //    - Filtra ventas canceladas (estatus != 6)
    //    - Ordena por hora e id para que salga “como pasó”
    $rowsMovs = VentaMovimiento::model()->executeQuery("
      SELECT
        DATE_FORMAT(vm.created, '%H:%i') AS hora,
        COALESCE(NULLIF(v.folio,''), v.id) AS folio,
        vm.forma_pago,
        COALESCE(NULLIF(vm.banco,''), '-') AS banco,
        COALESCE(NULLIF(vm.tarjeta_digitos,''), '') AS tarjeta_digitos,
        vm.monto
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE vm.tipo = 'ingreso'
        AND vm.corte_id = $corteId
        AND v.estatus != 6
      ORDER BY vm.created ASC, vm.id ASC
    ");

    // 2) Totales por forma de pago (para resumen)
    //    - Mismo filtro de canceladas, para que cuadre con el detalle
    $totales = VentaMovimiento::model()->executeQuery("
      SELECT
        vm.forma_pago,
        IFNULL(SUM(vm.monto),0) AS total,
        COUNT(*) AS num_movs
      FROM ec_venta_movimiento vm
      INNER JOIN ec_venta v ON v.id = vm.venta
      WHERE vm.tipo = 'ingreso'
        AND vm.corte_id = $corteId
        AND v.estatus != 6
      GROUP BY vm.forma_pago
      ORDER BY vm.forma_pago
    ");

    // 3) Armar map + conteos (para tu función resumenTotalesPorTipo)
    $map = ["efectivo"=>0,"tarjeta"=>0,"tarjetac"=>0,"vales"=>0];
    $cnt = ["efectivo"=>0,"tarjeta"=>0,"tarjetac"=>0,"vales"=>0];

    foreach($totales as $t){
      $fp = $t->forma_pago;
      if(isset($map[$fp])){
        $map[$fp] = floatval($t->total ?? 0);
        $cnt[$fp] = intval($t->num_movs ?? 0);
      }
    }



    // 4) PDF ticket 80mm usando TU clase (PDF_CorteTicket)
    $pdf = new PDF_CorteTicket();
    $pdf->AddPage();

    $pdf->headerCorte($sucursal);
    $pdf->corteInfo($corte, $operador);

    $pdf->resumenTotalesPorTipo($map, $cnt);

    // Depósito/caja (NUEVO)
    $topeCaja = 1100.0;
    $fondo = floatval($corte->fondo_caja ?? 0);
    $efectivoDia = floatval($map["efectivo"] ?? 0);
    $efectivoDisponible = $fondo + $efectivoDia;
    $efectivoParaManana = min($topeCaja, $efectivoDisponible);
    $depositoSugerido   = max(0, $efectivoDisponible - $topeCaja);

    $pdf->printDeposito($fondo, $efectivoDia, $efectivoDisponible, $efectivoParaManana, $depositoSugerido, $topeCaja);


    // IMPORTANTÍSIMO: imprimir el detalle con el método correcto
    $pdf->movimientosDetalle($rowsMovs);

    $pdf->footerCorte();

    $pdf->Output();
  }



}

class PDF_CorteTicket extends FPDF
{
  function __construct() {
    parent::__construct('P', 'mm', array(80, 320)); // 80mm ticket, alto “generoso”
    $this->SetMargins(2, 3, 2);
    $this->SetAutoPageBreak(true, 6);
  }

  function headerCorte($sucursal){
    $pageW   = $this->GetPageWidth();
    $usableW = $pageW - 4; // márgenes 2 y 2

    $logoPath = 'images/optica_logo.jpg';

    if(@file_exists($logoPath)){
      // ponlo al ancho útil
      $logoW = $usableW;
      $x = ($pageW - $logoW) / 2;

      // calcula alto aproximado para bajar el cursor correctamente
      $imgInfo = @getimagesize($logoPath);
      $logoH = 18; // fallback por si getimagesize falla
      if($imgInfo){
        $pxW = $imgInfo[0];
        $pxH = $imgInfo[1];
        if($pxW > 0){
          $logoH = ($logoW * $pxH) / $pxW; // mm aprox manteniendo proporción
        }
      }

      $y = $this->GetY();
      $this->Image($logoPath, $x, $y, $logoW); // sin alto: conserva proporción

      // BAJA el cursor: alto del logo + un margen
      $this->SetY($y + $logoH + 2);
    } else {
      $this->Ln(2);
    }

    $this->SetFont('Arial','B',10);
    $this->Cell(0,6,utf8_decode('CORTE / DETALLE'),0,1,'C');

    $this->SetFont('Arial','',8);
    $aTexto = $sucursal->nombre."\n".$sucursal->direccion;
    $this->MultiCell(0,4,utf8_decode($aTexto),0,'C');
    $this->Ln(1);

    $this->linea();
  }


  function corteInfo($corte, $operador){
    $this->SetFont('Arial','',8);

    $this->kv('Corte #', $corte->id);
    $this->kv('Fecha', date('d/m/Y', strtotime($corte->fecha)));

    $opName = trim($operador->nombre." ".$operador->apaterno." ".$operador->amaterno);
    $this->kvMulti('Cajero', $opName);

    $inicio = !empty($corte->created) ? date('d/m/Y H:i', strtotime($corte->created)) : '-';
    $fin    = !empty($corte->cerrado_at) ? date('d/m/Y H:i', strtotime($corte->cerrado_at)) : (!empty($corte->closed) ? date('d/m/Y H:i', strtotime($corte->closed)) : 'ABIERTO');

    $this->kv('Inicio', $inicio);
    $this->kv('Cierre', $fin);

    $this->Ln(1);
    $this->linea();
  }

  function resumenTotalesPorTipo($map, $conteos){
    $this->SetFont('Arial','B',9);
    $this->Cell(0,5,'RESUMEN',0,1,'C');
    $this->Ln(1);

    $this->SetFont('Arial','',8);

    $this->resRow('Efectivo',   $conteos['efectivo'] ?? 0,  $map['efectivo'] ?? 0);
    $this->resRow('Tarj Debito',$conteos['tarjeta'] ?? 0,   $map['tarjeta'] ?? 0);
    $this->resRow('Tarj Credito',$conteos['tarjetac'] ?? 0, $map['tarjetac'] ?? 0);
    $this->resRow('Vales',      $conteos['vales'] ?? 0,     $map['vales'] ?? 0);

    $total = floatval(($map['efectivo'] ?? 0) + ($map['tarjeta'] ?? 0) + ($map['tarjetac'] ?? 0) + ($map['vales'] ?? 0));

    $this->Ln(1);
    $this->SetFont('Arial','B',9);
    $this->Cell(0,5,'TOTAL: $'.number_format($total,2),0,1,'R');

    $this->Ln(1);
    $this->linea();
  }

  // IMPRIME TODOS LOS MOVIMIENTOS (uno por uno) y el banco/últimos 4 en línea extra
  function movimientosDetalle($rowsMovs){
    $this->SetFont('Arial','B',9);
    $this->Cell(0,5,'MOVIMIENTOS',0,1,'C');
    $this->Ln(1);

    // Encabezado compacto
    $this->SetFont('Arial','B',7);
    $this->Cell(10,4,'Hora',0,0,'L');
    $this->Cell(38,4,'Folio',0,0,'L');
    $this->Cell(12,4,'Pago',0,0,'L');
    $this->Cell(0,4,'Monto',0,1,'R');
    $this->Ln(1);

    $this->SetFont('Arial','',7);

    $pagoMap = [
      'efectivo' => 'EFEC',
      'tarjeta'  => 'T-DEB',
      'tarjetac' => 'T-CRED',
      'vales'    => 'VALES',
    ];

    foreach($rowsMovs as $m){
      $hora  = $m->hora ?? '--:--';
      $folio = trim((string)($m->folio ?? ''));
      $pago  = $pagoMap[$m->forma_pago] ?? strtoupper((string)$m->forma_pago);
      $monto = '$'.number_format(floatval($m->monto ?? 0),2);

      // Línea 1 (con folio multilínea si se ocupa)
      $x0 = $this->GetX();
      $y0 = $this->GetY();

      // Hora
      $this->Cell(10,4,$hora,0,0,'L');

      // Folio (puede envolver)
      $folioX = $this->GetX();
      $folioW = 38;

      // Calcula alto del bloque de folio
      $folioLines = $this->nbLines($folioW, $folio);
      $rowH = max(4, $folioLines * 3.5);

      $this->MultiCell($folioW,3.5,utf8_decode($folio),0,'L');

      // Regresa para pintar pago y monto alineados al alto del row
      $this->SetXY($folioX + $folioW, $y0);

      $this->Cell(12,$rowH,utf8_decode($pago),0,0,'L');
      $this->Cell(0,$rowH,$monto,0,1,'R');

      // Línea 2 banco (solo tarjetas)
      $esTarjeta = in_array($m->forma_pago, ['tarjeta','tarjetac'], true);
      if($esTarjeta){
        $banco = $m->banco ?? '-';
        $dig   = trim((string)($m->tarjeta_digitos ?? ''));
        $mask  = $dig !== '' ? (' ****'.$dig) : '';
        $this->SetFont('Arial','',7);
        $this->SetX(10); // sangría para que “caiga” bajo el folio
        $this->MultiCell(0,3.5,utf8_decode("Banco: ".$banco.$mask),0,'L');
      }

      // Separador suave
      $this->Ln(1);
    }

    $this->linea();
  }

  function footerCorte(){
    $this->SetFont('Arial','',8);
    $this->MultiCell(0,4,utf8_decode("IMPRESO: ".date('d/m/Y H:i')),0,'C');
    $this->Ln(1);
    $this->MultiCell(0,4,utf8_decode("CONSERVA ESTE CORTE PARA ACLARACIONES"),0,'C');
  }

  function printDeposito($fondo, $efectivoDia, $efectivoDisponible, $efectivoParaManana, $depositoSugerido, $topeCaja){
    $this->SetFont('Arial','B',9);
    $this->Cell(0,5,utf8_decode('DEPOSITO / CAJA'),0,1,'C');
    $this->Ln(1);

    $this->SetFont('Arial','',8);

    $this->Cell(0,4,utf8_decode('Fondo de caja: $'.number_format($fondo,2)),0,1,'L');
    $this->Cell(0,4,utf8_decode('Efectivo del dia: $'.number_format($efectivoDia,2)),0,1,'L');
    $this->Cell(0,4,utf8_decode('Efectivo disponible: $'.number_format($efectivoDisponible,2)),0,1,'L');

    $this->Ln(1);
    $this->Cell(0,4,utf8_decode('Se queda en caja (tope $'.number_format($topeCaja,2).'): $'.number_format($efectivoParaManana,2)),0,1,'L');

    $this->SetFont('Arial','B',9);
    $this->Cell(0,5,utf8_decode('Deposito sugerido: $'.number_format($depositoSugerido,2)),0,1,'R');

    $this->Ln(1);
    $this->linea();
  }


  // ----------------- Helpers -----------------
  private function kv($k, $v){
    $this->SetFont('Arial','B',8);
    $this->Cell(18,4,utf8_decode($k),0,0,'L');
    $this->SetFont('Arial','',8);
    $this->Cell(0,4,utf8_decode((string)$v),0,1,'R');
  }

  private function kvMulti($k, $v){
    $this->SetFont('Arial','B',8);
    $this->Cell(18,4,utf8_decode($k),0,0,'L');
    $this->SetFont('Arial','',8);
    $x = $this->GetX();
    $y = $this->GetY();
    $this->MultiCell(0,4,utf8_decode((string)$v),0,'R');
    // asegura separación
    $this->SetXY(2, max($this->GetY(), $y+4));
  }

  private function resRow($label, $count, $total){
    $this->Cell(0,4,utf8_decode($label.' ('.intval($count).')').' $'.number_format(floatval($total),2),0,1,'L');
  }

  private function linea(){
    $this->SetDrawColor(0,0,0);
    $y = $this->GetY();
    $this->Line(2, $y, 78, $y);
    $this->Ln(2);
  }

  // Calcula cuántas líneas ocupará un MultiCell (para alinear filas)
  private function nbLines($w, $txt){
    $cw = &$this->CurrentFont['cw'];
    if($w==0) $w = $this->w - $this->rMargin - $this->x;
    $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;

    $s = str_replace("\r",'',(string)$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n") $nb--;
    $sep = -1;
    $i = 0; $j = 0; $l = 0; $nl = 1;

    while($i < $nb){
      $c = $s[$i];
      if($c=="\n"){
        $i++; $sep=-1; $j=$i; $l=0; $nl++;
        continue;
      }
      if($c==' ') $sep = $i;
      $l += $cw[$c] ?? 0;
      if($l > $wmax){
        if($sep==-1){
          if($i==$j) $i++;
        } else {
          $i = $sep + 1;
        }
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else {
        $i++;
      }
    }
    return $nl;
  }
}
