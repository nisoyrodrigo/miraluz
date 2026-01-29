<?php
$noImage = $urlm("images/image-holder.jpg");
$cliente = new Cliente($venta->cliente);
$sucursal = new Sucursal($venta->sucursal);
$vendedor = new Operador("WHERE user != 1 AND user = ".$venta->user);

$abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$venta->id)[0]->total;

$estatusList = VentaEstatus::model()->findAll("");

$graduaciones = [
  'esfera' => range(-20.00, 9.00, 0.25), // Genera valores de -20.00 a 9.00 en incrementos de 0.25
  'cilindro' => range(-8.00, 0.00, 0.25), // Genera valores de -8.00 a 8.00 en incrementos de 0.25
  'eje' => range(0, 180, 5), // Genera valores de 0 a 180 en incrementos de 5
  'add' => range(0.75, 3.50, 0.25) // Genera valores de +0.75 a +3.50 en incrementos de 0.25
];

$graduacionOD = [
  'esfera' => $venta->od_esfera,
  'cilindro' => $venta->od_cilindro,
  'eje' => $venta->od_eje,
  'add' => $venta->od_add,
  'dnp' => $venta->od_dnp,
  'altura' => $venta->od_altura
];

$graduacionOI = [
  'esfera' => $venta->oi_esfera,
  'cilindro' => $venta->oi_cilindro,
  'eje' => $venta->oi_eje,
  'add' => $venta->oi_add,
  'dnp' => $venta->oi_dnp,
  'altura' => $venta->oi_altura
];

$regimenesFiscales = [
  '601' => '601 - General de Ley Personas Morales',
  '603' => '603 - Personas Morales con Fines no Lucrativos',
  '605' => '605 - Sueldos y Salarios e Ingresos Asimilados',
  '606' => '606 - Arrendamiento',
  '608' => '608 - Demás ingresos',
  '610' => '610 - Residentes en el Extranjero sin EP en México',
  '611' => '611 - Ingresos por Dividendos',
  '612' => '612 - Personas Físicas con Actividades Empresariales',
  '614' => '614 - Ingresos por Intereses',
  '615' => '615 - Régimen de los ingresos por obtención de premios',
  '616' => '616 - Sin obligaciones fiscales',
  '620' => '620 - Sociedades Cooperativas de Producción',
  '621' => '621 - Incorporación Fiscal',
  '622' => '622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
  '623' => '623 - Opcional para Grupos de Sociedades',
  '624' => '624 - Coordinados',
  '625' => '625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas',
  '626' => '626 - Régimen Simplificado de Confianza (RESICO PF)',
  '628' => '628 - Hidrocarburos',
  '630' => '630 - Enajenación de acciones en bolsa de valores'
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
        <input type="text" class="form-control" readonly value="<?=$venta->folio?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Fecha</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$venta->created?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Cliente</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$cliente->nombre." ".$cliente->apaterno." ".$cliente->amaterno;?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Nombre(razón social) factura</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->razon?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">RFC factura</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->rfc?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Régimen fiscal</label>
      <div class="col-lg-10">
        <input 
          type="text" 
          class="form-control" 
          readonly 
          value="<?= $regimenesFiscales[$data->regimen_fiscal] ?? $data->regimen_fiscal ?>"
        >
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Dirección fiscal</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->direccion_fiscal?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Código postal</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->codigo_postal?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Correo factura</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->correo?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Uso CFDI</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->uso_cfdi?>">
      </div>
    </div>

    <?php if(!empty($data->constancia_fiscal)){ ?>
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Constancia fiscal</label>
        <div class="col-lg-10">
          <a 
            href="<?=$url($data->constancia_fiscal)?>" 
            target="_blank" 
            class="btn btn-sm btn-danger"
          >
            <i class="fa fa-file-pdf-o"></i> Ver constancia
          </a>
        </div>
      </div>
    <?php } else { ?>
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Constancia fiscal</label>
        <div class="col-lg-10">
          <span class="text-muted">No adjunta</span>
        </div>
      </div>
    <?php } ?>

    <?php if(!empty($data->observaciones)){ ?>
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Observaciones</label>
        <div class="col-lg-10">
          <textarea class="form-control" rows="3" readonly><?=$data->observaciones?></textarea>
        </div>
      </div>
    <?php } ?>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Vendedor</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;?>">
      </div>
    </div>



    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Total</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$venta->total?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Abonado</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$venta->anticipo + $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Saldo</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$venta->saldo - $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Estatus</label>
      <div class="col-lg-10">
        <select class="form-control" id="estatus" name="estatus">
          <option value="pendiente" <?=("pendiente" == $data->estatus) ? "selected":"";?>>Pendiente</option>
          <option value="solicitada" <?=("solicitada" == $data->estatus) ? "selected":"";?>>Solicitada</option>
          <option value="enviada" <?=("enviada" == $data->estatus) ? "selected":"";?>>Enviada</option>
          <option value="cancelada" <?=("cancelada" == $data->estatus) ? "selected":"";?>>Cancelada</option>
        </select>
      </div>
    </div>


    <!-- Tabla de Graduación con los nombres corregidos -->
    <div id="seccionGraduacion">
      <!-- Tipo de Visión -->
      <div class="form-group row">
        <label class="col-lg-2 col-form-label">Tipo de Visión</label>
        <div class="col-lg-10">
          <input type="text" class="form-control" readonly value="<?=$venta->tipo_vision?>">
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
                <?=$venta->od_esfera?>
              </td>
              <td>
                <?=$venta->od_cilindro?>
              </td>
              <td>
                <?=$venta->od_eje?>
              </td>
              <td>
                <?=$venta->od_add?>
              </td>
              <td>
                <?=$venta->od_dnp?>
              </td>
              <td>
                <?=$venta->od_altura?>
              </td>
            </tr>
            <tr>
              <td>OI</td>
              <td>
                <?=$venta->oi_esfera?>
              </td>
              <td>
                <?=$venta->oi_cilindro?>
              </td>
              <td>
                <?=$venta->oi_eje?>
              </td>
              <td>
                <?=$venta->oi_add?>
              </td>
              <td>
                <?=$venta->oi_dnp?>
              </td>
              <td>
                <?=$venta->oi_altura?>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>




      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <?if($data->estatus != "enviada"){?>
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