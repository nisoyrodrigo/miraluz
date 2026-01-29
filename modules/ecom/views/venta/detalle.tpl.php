<?php
$noImage = $urlm("images/image-holder.jpg");
$cliente = new Cliente($data->cliente);
$sucursal = new Sucursal($data->sucursal);
$vendedor = new Operador("WHERE user != 1 AND user = ".$data->user);

$abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total;

$estatusList = VentaEstatus::model()->findAll("");

$graduaciones = [
  'esfera' => range(-20.00, 9.00, 0.25), // Genera valores de -20.00 a 9.00 en incrementos de 0.25
  'cilindro' => range(-8.00, 0.00, 0.25), // Genera valores de -8.00 a 8.00 en incrementos de 0.25
  'eje' => range(0, 180, 5), // Genera valores de 0 a 180 en incrementos de 5
  'add' => range(0.75, 3.50, 0.25) // Genera valores de +0.75 a +3.50 en incrementos de 0.25
];

$graduacionOD = [
  'esfera' => $data->od_esfera,
  'cilindro' => $data->od_cilindro,
  'eje' => $data->od_eje,
  'add' => $data->od_add,
  'dnp' => $data->od_dnp,
  'altura' => $data->od_altura
];

$graduacionOI = [
  'esfera' => $data->oi_esfera,
  'cilindro' => $data->oi_cilindro,
  'eje' => $data->oi_eje,
  'add' => $data->oi_add,
  'dnp' => $data->oi_dnp,
  'altura' => $data->oi_altura
];
?>

<style type="text/css">
.select2-container {
  z-index: 999999 !important;
}

.select2-dropdown {
  z-index: 9999999 !important;
}

.modal {
  overflow-y: auto; /* Permite desplazarse si el modal es muy largo */
}

body.modal-open {
  overflow: hidden; /* Evita el desplazamiento del fondo */
}
</style>

<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/saveEstatus")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Actualizar estatus</h4>
  </div>

  <div class="modal-body">
      
    <input type="hidden" name="id" value="<?=$data->id?>" />


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Nota</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->folio?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Fecha</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->created?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Cliente</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$cliente->nombre." ".$cliente->apaterno." ".$cliente->amaterno;?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Vendedor</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;?>">
      </div>
    </div>



    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Total</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->total?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Abonado</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->anticipo + $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Saldo</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->saldo - $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Estatus</label>
      <div class="col-lg-10">
        <select class="form-control" id="estatus" name="estatus" readonly>
          <?foreach ($estatusList as $key => $value) {?>
          <option value="<?=$value->id?>" <?=($value->id == $data->estatus) ? "selected":"";?>><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row" id="estatus-garantia-container" style="display: none;">
      <label class="col-lg-2 col-form-label">Estatus Garantía</label>
      <div class="col-lg-10">
        <select class="form-control" id="estatus_garantia" name="estatus_garantia">
          <?foreach ($estatusList as $key => $value) {?>
          <option value="<?=$value->id?>" <?=($value->id == $data->estatus_garantia) ? "selected":"";?>><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
    </div>

    <!-- Tabla de Graduación con los nombres corregidos -->
    <div id="seccionGraduacion">
      <!-- Tipo de Visión -->
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Tipo de Visión</label>
        <div class="col-lg-10">
          <input type="text" class="form-control" readonly value="<?=$data->tipo_vision?>">
        </div>
      </div>

      <!-- Tabla de Graduación -->
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Graduación</label>
        <div class="col-lg-10">
          <table class="table table-bordered">
            <tr>
              <th></th>
              <th>Esfera</th>
              <th>Cilindro</th>
              <th>Eje</th>
              <th class="col-adicion">ADD</th>
              <th>DNP</th>
              <th class="col-altura">Altura</th>
            </tr>
            <tr>
              <td>OD</td>
              <td>
                <?=$data->od_esfera?>
              </td>
              <td>
                <?=$data->od_cilindro?>
              </td>
              <td>
                <?=$data->od_eje?>
              </td>
              <td>
                <?=$data->od_add?>
              </td>
              <td>
                <?=$data->od_dnp?>
              </td>
              <td>
                <?=$data->od_altura?>
              </td>
            </tr>
            <tr>
              <td>OI</td>
              <td>
                <?=$data->oi_esfera?>
              </td>
              <td>
                <?=$data->oi_cilindro?>
              </td>
              <td>
                <?=$data->oi_eje?>
              </td>
              <td>
                <?=$data->oi_add?>
              </td>
              <td>
                <?=$data->oi_dnp?>
              </td>
              <td>
                <?=$data->oi_altura?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>




      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
  </div>

 </form>

<script type="text/javascript">

  $(document).ready(function(){

    const entregadoId = 4; // Reemplaza con el ID real del estatus "Entregado"
    const garantiaId = 5; // Reemplaza con el ID real del estatus "Garantía"

    // Detectar el estatus actual al cargar
    const estatusActual = $('#estatus').val();

    if (estatusActual == entregadoId) {
      // Si el estatus actual es "Entregado", restringe las opciones a "Entregado" y "Garantía"
      $('#estatus')
        .find('option')
        .not(`[value="${entregadoId}"], [value="${garantiaId}"]`) // Solo permite "Entregado" y "Garantía"
        .remove();
    }

    if (estatusActual == garantiaId) {
      // Si el estatus actual es "Entregado", restringe las opciones a "Entregado" y "Garantía"
      $('#estatus')
        .find('option')
        .not(`[value="${garantiaId}"]`) // Solo permite "Entregado" y "Garantía"
        .remove();
    }


    function initSelect2() {
      $('.select2').select2({
        dropdownParent: $('#formEdit').closest('.modal'),
        width: '100%' // Se asegura que los select ocupen todo el ancho
      });
    }

    $('#myModal').on('shown.bs.modal', function () {
      initSelect2();
      toggleEstatusGarantia(); // Asegura que la visibilidad esté sincronizada
    });


    $("#formEdit").submit(function(){
      $("#btn-guardar").html("<i class='fa fa-spinner fa-spin '></i> Guardando");
    });

    $('#formEdit').ajaxForm(function(response) { 
      console.log(response);
      $("#btn-guardar").html("Guardar");
      if(response.error == undefined){
        alertMessage("Información guardada satisfactoriamente.", "success");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();
      } else {
        alertMessage(response.error);
        console.log("Error al guardar información");
      }
    });



  });


  function actualizarGraduacion() {
    const tipoVision = document.querySelector('input[name="gtipo_vision"]:checked').value;

    if (tipoVision === 'vision_sencilla') {
      $('.col-adicion, .col-altura').hide();
    } else {
      $('.col-adicion, .col-altura').show();
    }
  }

  /*––––––––––––––––––––––– /Producto fotos ––––––––––––––––––––––-––––––*/
</script>