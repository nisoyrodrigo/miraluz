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

$reclutador = new Reclutador("WHERE user = ".$this->user->id);

$objetos = array();

$miId = $this->user->id;
$objReclutador = new Reclutador("WHERE user = ".$miId);
$auxWhere = "WHERE id IN(".$objReclutador->plazas.")";
if($miId == 1) $auxWhere = "";

$plazas = Plaza::model()->findAll($auxWhere);
/*
foreach ($plazas as $key => $value) {
  $contratados = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza = ".$value->id." AND p.estatus = (11)")[0]->total;
  $enproceso = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza = ".$value->id." AND p.estatus IN(2,3,4,5,6,7,8,9,10,12)")[0]->total;
  $sinasignar = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza = ".$value->id." AND p.reclutador_user IS NULL")[0]->total;
  $auxObj = array();
  $auxObj["id"] = $value->id;
  $auxObj["nombre"] = $value->nombre;
  $auxObj["color"] = $value->color;
  $auxObj["contratados"] = $contratados;
  $auxObj["enproceso"] = $enproceso;
  $auxObj["sinasignar"] = $sinasignar;
  $objetos[] = $auxObj;
}

$sinplaza =  Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza IS NULL")[0]->total;

*/
$mediosDifusion = MedioDifusion::model()->findAll();

$zonas = Zona::model()->findAll();


?>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Bienvenido <strong><?=$reclutador->nombre;?> <?=$reclutador->apaterno;?> <?=$reclutador->amaterno;?></strong></h2><br>
    <a class="btn btn-primary" href="javascript:void(0);" onclick="actualizaInformacion();" style="color: white !important;">Mi información</a>

  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">

    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-content">
          <div class="table-responsive">
            <table id="tabla-data" class="table data-table table-dashboard-oxxo">
              <tr>
                <td class="bg-purple text-center" colspan="3">Contarás con tu pago semanal de forma fija. <br><strong>¡Cuentas con tu pago de forma segura siempre!</strong></td>
                <td class="bg-purple text-center">
                  <div class="form-group">
                    <select class="form-control" name="plaza" style="color: black !important;">
                      <option value="" selected>Selecciona una plaza...</option>
                      <?foreach ($plazas as $key => $value) {?>
                      <option value="<?=$value->id?>"><?=$value->nombre?></option>
                      <?}?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="4" class="mb-2"></td>
              </tr>
              <tr>
                <td class="" width="25%"></td>
                <td class="bg-yellow1" width="25%">DIARIO</td>
                <td class="bg-yellow2" width="25%">SEMANAL</td>
                <td class="bg-purple2" width="25%">MENSUAL</td>
              </tr>
              <tr>
                <td class="td-title">
                  <table width="100%">
                    <tr>
                      <td class="td-title">Ingreso asegurado</td>
                    </tr>
                  </table>
                </td>
                <td>
                  <table width="100%">
                    <tr>
                      <td class="td-title">Salario</td>
                      <td class="td-title">Incentivo de comida</td>
                      <td class="td-title">Total Bruto</td>
                    </tr>
                  </table>
                </td>
                <td>
                  <table width="100%">
                    <tr>
                      <td class="td-title">Salario</td>
                      <td class="td-title">Incentivo de comida</td>
                      <td class="td-title">Total Bruto</td>
                    </tr>
                  </table>
                </td>
                <td>
                  <table width="100%">
                    <tr>
                      <td class="td-title">Salario</td>
                      <td class="td-title">Incentivo de comida</td>
                      <td class="td-title">Total Bruto</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="td-title2">
                  <table width="100%">
                    <tr>
                      <td class="td-title2">Al ingresar</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow1">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">$ 197.46</td>
                      <td class="">$  12.34</td>
                      <td class="">$ 209.80</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">$ 1,382.22</td>
                      <td class="">$    74.05</td>
                      <td class="">$ 1,456.27</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-purple2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">$ 5,923.80</td>
                      <td class="">$   296.19</td>
                      <td class="">$ 6,219.99</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="td-title2">
                  <table width="100%">
                    <tr>
                      <td class="td-title2">Al cumplir 3 meses</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow1">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 1 de cursos</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 1 de cursos</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-purple2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 1 de cursos</td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td class="td-title2">
                  <table width="100%">
                    <tr>
                      <td class="td-title2">Al cumplir 6 meses</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow1">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 2 de cursos</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-yellow2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 2 de cursos</td>
                    </tr>
                  </table>
                </td>
                <td class="bg-purple2">
                  <table width="100%" class="table-hover">
                    <tr>
                      <td class="">+ bloque 2 de cursos</td>
                    </tr>
                  </table>
                </td>
              </tr>

              <tr>
                <td colspan="4" class="mb-2"></td>
              </tr>

              <tr>
                <td class="bg-yellow1 text-white text-center">
                  ¡Siempre remuneramos tu trabajo!
                </td>
                <td colspan="3" class=" bg-yellow1 text-white text-center">
                  ¡Valoramos el apoyo que nos brindas al asegurar estar disponible y brindar el mejor servicio para nuestros clientes!
                </td>
              </tr>

              <tr>
                <td colspan="4" class="mb-2"></td>
              </tr>

            </table>


            <table id="tabla-data2" class="table data-table table-dashboard-oxxo mt-2">
              <tr>
                <td width="52%" class="td-title text-left">Pago adicional</td>
                <td width="16%" class="td-title text-left">Al ingresar</td>
                <td width="16%" class="td-title text-left">Al cumplir 3 meses</td>
                <td width="16%" class="td-title text-left">Al cumplir 6 meses</td>
              </tr>
              <tr>
                <td width="52%" class="td-title2 text-left">Si trabajas tu día de descanso</td>
                <td width="16%" class="">$ 394.92</td>
                <td width="16%" class="">$ 411.92</td>
                <td width="16%" class="">$ 434.92</td>
              </tr>
              <tr>
                <td width="52%" class="td-title2 text-left">Si trabajas un día festivo(conforme a la ley).</td>
                <td width="16%" class="">$ 394.92</td>
                <td width="16%" class="">$ 411.92</td>
                <td width="16%" class="">$ 394.92</td>
              </tr>
              <tr>
                <td width="52%" class="td-title2 text-left">Por trabajar el domingo</td>
                <td width="16%" class="">$  49.37</td>
                <td width="16%" class="">$  51.49</td>
                <td width="16%" class="">$  54.37</td>
              </tr>
            </table>




            <table id="tabla-data3" class="table data-table table-dashboard-oxxo mt-4">
              <tr>
                <td colspan="2" class="bg-purple">Porque siempre recompensamos tu enfoque al cliente, entre más vendas y cumplas con las metas en tu tienda ¡Tú ganas!</td>
                <td class="bg-purple">TIEMPO EXTRA</td>
                <td class="bg-purple">Mañana<br>7:00am a 3:00pm</td>
                <td class="bg-purple">Tarde<br>3:00pm a 11:00pm</td>
                <td class="bg-purple">Noche<br>11:00pm a 7:00am</td>
              </tr>
              <tr>
                <td class="td-title text-left">Incentivo de merma</td>
                <td class="td-title text-left">Ganas</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Venta sugerida</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Incentivo de servicios</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Incentivo de referidos</td>
                <td class="text-left">$ 300.00 x cu</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Activación de tarjeta SPIN</td>
                <td class="text-left">$ 3.50</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Depósitos y retiros SPIN</td>
                <td class="text-left">$ 0.50</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Activación de tarjeta OXXO Premia</td>
                <td class="text-left">$ 3.50</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
              <tr>
                <td class="td-title2 text-left">Incentivo de venta con uso de tarjeta OXXO Premia</td>
                <td class="text-left">%  0.4</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
                <td class="text-left">$ 0.00</td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
function actualizaInformacion(){
  modalBootStrap('<?=$url("ecom/".$this->interfaz."/actualizaInformacion")?>','', '60');
}
</script>