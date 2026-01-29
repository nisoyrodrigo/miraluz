<?
function tiene_permisos($seccion, $permiso, $rol){
  if($rol == 1) return true;
  $result = false;
  $qry = "SELECT us.permiso FROM user_section us LEFT JOIN section s ON us.section = s.id WHERE us.rol = ".$rol." AND s.name = '".$seccion."' AND s.action = '".$permiso."'";
  $permiso = UserSection::model()->executeQuery($qry);
  $result = ($permiso[0]->permiso == 1) ? true:false;
  return $result;
}


$aMeses = array(
  1  => "Enero",
  2  => "Febrero",
  3  => "Marzo",
  4  => "Abril",
  5  => "Mayo",
  6  => "Junio",
  7  => "Julio",
  8  => "Agosto",
  9  => "Septiembre",
  10 => "Octubre",
  11 => "Noviembre",
  12 => "Diciembre"
);
?>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Bienvenido <strong><?=$reclutador->nombre;?> <?=$reclutador->apaterno;?> <?=$reclutador->amaterno;?></strong></h2><br>
    <a class="btn btn-primary" href="javascript:void(0);" onclick="actualizaInformacion();" style="color: white !important;">Mi información</a>

  </div>
</div>

<?
if($user->rol != "13"){
?>
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">


  </div>
</div>

<script type="text/javascript">

</script>

<?}?>
<script type="text/javascript">
function actualizaInformacion(){
  modalBootStrap('<?=$url("ecom/".$this->interfaz."/actualizaInformacion")?>','', '60');
}
</script>