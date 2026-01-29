<?php
$noImage = $urlm("images/image-holder.jpg");
$cliente = new Cliente($data->cliente);
$sucursal = new Sucursal($data->sucursal);
$vendedor = new Operador("WHERE user = ".$data->user);

$abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total;

$estatusList = VentaEstatus::model()->findAll("ORDER BY orden ASC");

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

$estatus = new VentaEstatus($data->estatus);
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

    <?if($data->estatus == "6"){?>
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Estatus</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$estatus->nombre?>">
      </div>
    </div>
    <?}?>

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
        <select class="form-control" id="estatus" name="estatus" <?=($data->estatus == "6") ? "disabled":"";?>>
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
    <div id="seccionGraduacion" style="display: none;">
      <!-- Tipo de Visión -->
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Tipo de Visión</label>
        <div class="col-lg-10">
          <label><input type="radio" name="gtipo_vision" value="vision_sencilla" checked onchange="actualizarGraduacion()"> Visión sencilla</label>
          <label><input type="radio" name="gtipo_vision" value="flap_top" onchange="actualizarGraduacion()"> Flap top</label>
          <label><input type="radio" name="gtipo_vision" value="blend" onchange="actualizarGraduacion()"> Blend</label>
          <label><input type="radio" name="gtipo_vision" value="progresivo" onchange="actualizarGraduacion()"> Progresivo</label>
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
                <select name="god_esfera" class="form-control select2">
                  <?php foreach ($graduaciones['esfera'] as $value): 
                    $formattedValue = $value > 0 ? '+' . number_format($value, 2) : number_format($value, 2);
                    $selected = ($value == $graduacionOD['esfera']) ? "selected" : "";
                  ?>
                    <option value="<?= number_format($value, 2) ?>" <?=$selected?>><?= $formattedValue ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="god_cilindro" class="form-control select2">
                  <?php foreach ($graduaciones['cilindro'] as $value): 
                    $selected = ($value == $graduacionOD['cilindro']) ? "selected" : "";
                    ?>
                    <option value="<?= number_format($value, 2) ?>" <?=$selected?>><?= number_format($value, 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="god_eje" class="form-control select2">
                  <?php foreach ($graduaciones['eje'] as $value): 

                    $selected = ($value == $graduacionOD['eje']) ? "selected" : "";?>
                    <option value="<?= $value ?>" <?=$selected?>><?= $value ?>°</option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td class="col-adicion">
                <select name="god_add" class="form-control select2">
                  <?php foreach ($graduaciones['add'] as $value): 

                    $selected = ($value == $graduacionOD['add']) ? "selected" : ""; ?>
                    <option value="<?= number_format($value, 2) ?>" <?=$selected?>><?= number_format($value, 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input type="text" class="form-control" name="god_dnp" value="<?= $graduacionOD['dnp'] ?>"></td>
              <td class="col-altura"><input type="text" class="form-control" name="god_altura" value="<?= $graduacionOD['altura'] ?>"></td>
            </tr>
            <tr>
              <td>OI</td>
              <td>
                <select name="goi_esfera" class="form-control select2">
                  <?php foreach ($graduaciones['esfera'] as $value): 
                    $formattedValue = $value > 0 ? '+' . number_format($value, 2) : number_format($value, 2);
                    $selected = ($value == $graduacionOI['esfera']) ? "selected" : "";
                    ?>
                    <option value="<?= number_format($value, 2) ?>" <?=$selected?>><?= $formattedValue ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="goi_cilindro" class="form-control select2">
                  <?php foreach ($graduaciones['cilindro'] as $value): ?>
                    <option value="<?= number_format($value, 2) ?>"><?= number_format($value, 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td>
                <select name="goi_eje" class="form-control select2">
                  <?php foreach ($graduaciones['eje'] as $value): ?>
                    <option value="<?= $value ?>"><?= $value ?>°</option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td class="col-adicion">
                <select name="goi_add" class="form-control select2">
                  <?php foreach ($graduaciones['add'] as $value): ?>
                    <option value="<?= number_format($value, 2) ?>"><?= number_format($value, 2) ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
              <td><input type="text" class="form-control" name="goi_dnp" value="<?= $graduacionOI['dnp'] ?>"></td>
              <td class="col-altura"><input type="text" class="form-control" name="goi_altura" value="<?= $graduacionOI['altura'] ?>"></td>
            </tr>
          </table>
        </div>
      </div>
    </div>




      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <?if($data->estatus != "6"){?>
    <button id="btn-guardar" type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Guardando">Guardar</button>
    <?}?>
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

    function toggleEstatusGarantia() {
      const estatusActual = $('#estatus').val();
      if (estatusActual == garantiaId) {
        $('#estatus-garantia-container').show(); // Muestra el campo
        $('#estatus_garantia').prop('disabled', false); // Habilita edición
        $('#seccionGraduacion').show();
      } else {
        $('#estatus-garantia-container').hide(); // Oculta el campo
        $('#estatus_garantia').prop('disabled', true); // Deshabilita edición
        $('#seccionGraduacion').hide();
      }
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



    toggleEstatusGarantia();

    $('#estatus').on('change', function () {
      toggleEstatusGarantia();
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