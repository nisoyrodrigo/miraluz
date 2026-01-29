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

$reclutador = new RTalento("WHERE user = ".$this->user->id);

$objetos = array();

$miId = $this->user->id;
$objReclutador = new RTalento("WHERE user = ".$miId);
$auxWhere = "WHERE id IN(".$objReclutador->plazas.")";
if($miId == 1) $auxWhere = "";

$plazas = PlazaQS::model()->findAll($auxWhere);
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
    <?if(tiene_permisos("Default", "GraficaQS", $user->rol)):?>

    <div class="col-md-6 col-xs-12">
      <div class="panel panel-dashboard">
        <div class="panel-heading">
          POSTULACIONES POR PLAZA
        </div>
        <div class="panel-body">
          <canvas id="grafica1" height="200"></canvas>
        </div>
        <div class="panel-footer">
          <select id="plazas_1" class="form-control select2" multiple="multiple">
            <option value="0">Nacional</option>
          <?foreach ($plazas as $key => $value) {?>
            <option value="<?=$value->id?>" class="oopt"><?=$value->nombre?></option>
          <?}?>
          </select>
          <input type="text" id="fechainicio_1" class="form-control datepicker mt-2" placeholder="Fecha" autocomplete="off">
          <a class="btn btn-primary pull-righ mt-2" href="javascript: void(0);" onclick="getPostulacionesPlaza();">Consultar</a>
        </div>
      </div>

    </div>

    <div class="col-md-12 col-xs-12">
      <div class="panel panel-dashboard">
        <div class="panel-heading">
          RESUMEN DE POSTULACIONES
        </div>
        <div class="panel-body" id="tablaresumen1">
        </div>
        <div class="panel-footer">
          <select id="plazas_2" class="form-control select2" multiple="multiple">
            <option value="0">Nacional</option>
          <?foreach ($plazas as $key => $value) {?>
            <option value="<?=$value->id?>" class="oopt"><?=$value->nombre?></option>
          <?}?>
          </select>
          <a class="btn btn-primary pull-righ mt-2" href="javascript: void(0);" onclick="getResumen();">Consultar</a>
        </div>
      </div>
    </div>

    <?endif;?>

  </div>
</div>

<script type="text/javascript">
var ctx1 = document.getElementById("grafica1").getContext("2d");

var gra1, gra2 = null;

var graficaOpciones = {
  responsive: true,
  scales: {
    xAxes: [ {
      ticks: {
        autoSkip: false
      }
    }],
  },
  plugins: {
    datalabels: {
      anchor: 'end',
      align: 'top',
      formatter: Math.round,
      font: {
        weight: 'bold'
      }
    }
  }
};

$(function () {

  $('.select2z').on("select2:select", function (e) { 
    var data = e.params.data.text;
    console.log(data);
    if(data=='Nacional'){
      $(".select2z > .oopt").removeAttr("selected");
      $(".select2z").trigger("change");
    }
  });


  $('.datepicker').datepicker({
    format: 'yyyy-mm-dd'
  });
  $('.select2').select2({
    placeholder: 'Selecciona una o más plazas...'
  });
  $('.select22').select2({
    placeholder: 'Selecciona uno o más medios...'
  });
  $('.select2z').select2({
    placeholder: 'Selecciona una o más zonas...'
  });
});

function getContrataciones(){
  var plazas = $("#plazas_1").val();
  var finicio = $("#fechainicio_1").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaContrataciones")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra1 ==null){
          gra1 = new Chart(ctx1, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra1.data = graficaData;
          gra1.update();
        }
      }
    });
  }

}

function getPostulacionesPlaza(){
  var plazas = $("#plazas_1").val();
  var finicio = $("#fechainicio_1").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaPostulacionesPlaza")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra1 ==null){
          gra1 = new Chart(ctx1, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra1.data = graficaData;
          gra1.update();
        }
      }
    });
  }

}

function getEnProceso(){
  var plazas = $("#plazas_2").val();
  var finicio = $("#fechainicio_2").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnProceso")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra2 ==null){
          gra2 = new Chart(ctx2, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra2.data = graficaData;
          gra2.update();
        }
      }
    });
  }

}

function getEnProcesoZona(){
  var plazas = $("#plazas_2z").val();
  var finicio = $("#fechainicio_2z").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más zonas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnProcesoZona")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra2z ==null){
          gra2z = new Chart(ctx2z, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra2z.data = graficaData;
          gra2z.update();
        }
      }
    });
  }

}

function getSinAsignar(){
  var plazas = $("#plazas_3").val();
  var finicio = $("#fechainicio_3").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaSinAsignar")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra3 ==null){
          gra3 = new Chart(ctx3, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra3.data = graficaData;
          gra3.update();
        }
      }
    });
  }

}

function getSinAsignarZona(){
  var plazas = $("#plazas_3z").val();
  var finicio = $("#fechainicio_3z").val();
  var ffin = $("#fechafin_3z").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaSinAsignarZona")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra3z ==null){
          gra3z = new Chart(ctx3z, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra3z.data = graficaData;
          gra3z.update();
        }
      }
    });
  }

}

function getEnCartera(){
  var plazas = $("#plazas_4").val();
  var finicio = $("#fechainicio_4").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnCartera")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra4 ==null){
          gra4 = new Chart(ctx4, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra4.data = graficaData;
          gra4.update();
        }
      }
    });
  }
}

function getEnMiCartera(){
  var plazas = $("#plazas_4m").val();
  var finicio = $("#fechainicio_4m").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnMiCartera")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra4m ==null){
          gra4m = new Chart(ctx4m, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra4m.data = graficaData;
          gra4m.update();
        }
      }
    });
  }

}

function getEnCarteraZona(){
  var plazas = $("#plazas_4z").val();
  var finicio = $("#fechainicio_4z").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnCarteraZona")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra4z ==null){
          gra4z = new Chart(ctx4z, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra4z.data = graficaData;
          gra4z.update();
        }
      }
    });
  }

}


function getEnMiCarteraZona(){
  var plazas = $("#plazas_4zm").val();
  var finicio = $("#fechainicio_4zm").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/graficaEnMiCarteraZona")?>", {"plazas":plazas, 'fecha':finicio}, function (response){
      console.log(response);
      if(response.data){

        var graficaData = {
          labels: [],
          datasets: response.data
        };
        if(gra4zm ==null){
          gra4zm = new Chart(ctx4zm, {type: 'bar', data: graficaData, options:graficaOpciones});
        } else {
          gra4zm.data = graficaData;
          gra4zm.update();
        }
      }
    });
  }

}


function getResumen(){
  var plazas = $("#plazas_2").val();
  var sError = "";

  if(plazas == ""){
    sError += "Debes elegir una o más plazas.\n";
  }
  if(sError != ""){
    alertMessage(sError);
  } else {
    $.post("<?=$url("ecom/".$this->interfaz."/getResumen")?>", {"plazas": plazas}, function (response){
      $("#tablaresumen1").html(response);
    });
  }

}

</script>


<script type="text/javascript">
function actualizaInformacion(){
  modalBootStrap('<?=$url("ecom/".$this->interfaz."/actualizaInformacion")?>','', '60');
}
</script>