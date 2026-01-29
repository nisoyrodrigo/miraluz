<?

$aMeses = array(
  "1"=>"Enero",
  "2" => "Febrero",
  "3" => "Marzo",
  "4" => "Abril",
  "5" => "Mayo",
  "6" => "Junio",
  "7" => "Julio",
  "8" => "Agosto",
  "9" => "Septiembre",
  "10" => "Octubre",
  "11" => "Noviembre",
  "12" => "Diciembre",
);
$sPlazas = implode(",", $plazas);
if($sPlazas == "0"){
  $aWhere = "WHERE 1 = 1";
} else {
  $aWhere = "WHERE id IN($sPlazas)";
}


if($sPlazas== "0"){
?>
<div class="table-responsive">
  <table id="tabla-data" class="table table-striped table-hover data-table">
    <tr>
      <th></th>
      <th># postulaciones</th>
      <th>Total Encargado</th>
      <th>Total aprobados</th>
      <th>Total Lider</th>
      <th>Total aprobados</th>
    </tr>
    <?for ($i=0; $i < date('n', time()); $i++) {
    $mes = $i + 1;
    $mes = ($mes < 10) ? "0".$mes:$mes;
    $dateMonth = date('Y', time())."-".$mes;
    $totalPostulados = Postulacion::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_postulacion WHERE DATE_FORMAT(fecha, '%Y-%m') = '$dateMonth'")[0]->total;
    $totalPostuladosEncargado = Postulacion::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_postulacion WHERE puesto = 'encargado' AND DATE_FORMAT(fecha, '%Y-%m') = '$dateMonth'")[0]->total;
    $totalPostuladosLider = Postulacion::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_postulacion WHERE puesto = 'lider' AND DATE_FORMAT(fecha, '%Y-%m') = '$dateMonth'")[0]->total;
    $totalPostuladosEncargadoAceptados = Postulacion::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_postulacion WHERE estatus = 4 AND puesto = 'encargado' AND DATE_FORMAT(fecha, '%Y-%m') = '$dateMonth'")[0]->total;
    $totalPostuladosLiderAceptados = Postulacion::model()->executeQuery("SELECT COUNT(*) AS total FROM ec_postulacion WHERE estatus = 4 AND puesto = 'lider' AND DATE_FORMAT(fecha, '%Y-%m') = '$dateMonth'")[0]->total;
    ?>
    <tr>
      <td><?=$aMeses[$i + 1]?></td>
      <td><?=$totalPostulados?></td>
      <td><?=$totalPostuladosEncargado?></td>
      <td><?=$totalPostuladosEncargadoAceptados?></td>
      <td><?=$totalPostuladosLider?></td>
      <td><?=$totalPostuladosLiderAceptados?></td>
    </tr>
    <?}?>
  </table>
</div>
<?
} else {
  $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");  

}

?>