<?php
$noImage = $urlm("images/image-holder.jpg");
$operador = new Operador("WHERE user = ".$this->user->id);
$sucursales = Sucursal::model()->findAll("WHERE id IN(".$operador->sucursales.")");

$fechaDefault = !empty($data->fecha) ? date('Y-m-d', strtotime($data->fecha)) : date('Y-m-d');

?>


<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/save")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Corte del día (<?=date('d/m/Y', time());?>)</h4>
  </div>

  <div class="modal-body">

    <!-- HIDDENs -->
    <input type="hidden" name="lab_ids" id="lab_ids" value="">
    <input type="hidden" name="deposito" id="deposito" value="0">

    <!-- SUCURSAL -->
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Sucursal</label>
      <div class="col-lg-10">
        <select class="form-control" name="sucursal" id="sucursal">
          <option value="">Selecciona una sucursal...</option>
          <?foreach ($sucursales as $value) {?>
            <option value="<?=$value->id?>"><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
    </div>

    <!-- FECHA -->
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Fecha</label>
      <div class="col-lg-10">
        <input type="date"
               class="form-control"
               id="fecha"
               name="fecha"
               value="<?=$fechaDefault?>"
               readonly>
      </div>
    </div>

    <hr>

    <!-- RESUMEN + DEPOSITO -->
    <div class="row">

      <!-- RESUMEN -->
      <div class="col-md-6">
        <div class="well" style="margin-bottom:10px;">
          <h4 style="margin-top:0;">Resumen del día</h4>

          <p style="font-size:16px;">
            Vendiste (ingresos):
            <b id="total_dia">$0.00</b>
          </p>

          <div style="display:flex; justify-content:space-between;">
            <span>Efectivo:</span>
            <b id="t_efectivo">$0.00</b>
          </div>

          <div style="display:flex; justify-content:space-between;">
            <span>Tarjeta débito:</span>
            <b id="t_tarjeta">$0.00</b>
          </div>

          <div style="display:flex; justify-content:space-between;">
            <span>Tarjeta crédito:</span>
            <b id="t_tarjetac">$0.00</b>
          </div>

          <div style="display:flex; justify-content:space-between;">
            <span>Vales:</span>
            <b id="t_vales">$0.00</b>
          </div>
        </div>
      </div>

      <!-- DEPOSITO -->
      <div class="col-md-6">
        <div class="well" style="margin-bottom:0;">
          <h4 style="margin-top:0;">Caja y Depósito</h4>

          <div style="display:flex; justify-content:space-between; color:#888;">
            <span>Fondo del día anterior:</span>
            <b id="fondo_caja">$0.00</b>
          </div>

          <div style="display:flex; justify-content:space-between;">
            <span>+ Efectivo del día:</span>
            <b id="efectivo_dia">$0.00</b>
          </div>

          <hr style="margin:8px 0;">

          <div style="display:flex; justify-content:space-between;">
            <span>Efectivo disponible:</span>
            <b id="efectivo_disponible">$0.00</b>
          </div>

          <div style="display:flex; justify-content:space-between; color:#888;">
            <span>Te quedas para mañana (tope):</span>
            <b id="tope_caja">$1,100.00</b>
          </div>

          <div style="display:flex; justify-content:space-between; font-size:16px; margin-top:5px;">
            <span>Depósito sugerido:</span>
            <b id="deposito_sugerido" style="color:#c0392b;">$0.00</b>
          </div>
        </div>
      </div>

    </div>

    <hr>

    <!-- LABORATORIO -->
    <h4>Listo para enviar a laboratorio (≥ 30%)</h4>

    <div class="table-responsive">
      <table class="table table-sm table-striped" id="tabla_lab">
        <thead>
          <tr>
            <th><input type="checkbox" id="chk_all_lab"></th>
            <th>Folio</th>
            <th>Cliente</th>
            <th>Total</th>
            <th>Pagado</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>


  </div>


  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button id="btn-guardar" type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Guardando">Guardar</button>
  </div>

 </form>

<script type="text/javascript">
  $(document).ready(function(){

    $('.select2').select2({
      dropdownParent: $('#myModal')
    });


    /*––––––––––––––––––––––– Distintivo ––––––––––––––––––––––-––––––*/
    $(".portada-foto").click(function(){$("#portada").click();});
    /*––––––––––––––––––––––– /Distintivo ––––––––––––––––––––––-––––––*/
    $("#portada").change(function() {
      var input = $(this)[0];
      var url = $(this).val();
      var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
      if (input.files && input.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg" || ext == "webp" || ext == "svg")){
        var reader = new FileReader();

        reader.onload = function (e) {
           $('#portada-img').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
      }
      else{
        $('#portada-img').attr('src', '<?=$noImage?>');
      }
    });

    $('#formEdit').on('submit', function(){
      const ids = $('.chk_lab:checked').map(function(){ return $(this).val(); }).get();
      $('#lab_ids').val(ids.join(','));
      $("#btn-guardar").html("<i class='fa fa-spinner fa-spin '></i> Guardando");
    });


    $("#formEdit").submit(function(){
      const ids = $('.chk_lab:checked').map(function(){ return $(this).val(); }).get();
      $('#lab_ids').val(ids.join(','));
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


  function money(n){
    n = parseFloat(n || 0);
    return n.toLocaleString('es-MX', { style:'currency', currency:'MXN' });
  }

  function renderTarjetas(rows){
    if(!rows || rows.length === 0){
      $('#tabla_tarjetas').html('<small>No hay pagos con tarjeta en ese día.</small>');
      return;
    }

    let html = '<table class="table table-sm table-bordered">';
    html += '<thead><tr><th>Tipo</th><th>Banco</th><th>Últimos 4</th><th>Total</th></tr></thead><tbody>';

    rows.forEach(r => {
      let tipo = (r.forma_pago === 'tarjeta') ? 'Débito' : 'Crédito';
      html += `<tr>
        <td>${tipo}</td>
        <td>${r.banco || '-'}</td>
        <td>${r.tarjeta_digitos || '-'}</td>
        <td>${money(r.total)}</td>
      </tr>`;
    });

    html += '</tbody></table>';
    $('#tabla_tarjetas').html(html);
  }


  function renderPendientesLab(rows){
    const $tb = $('#tabla_lab tbody');
    $tb.empty();

    if(!rows || rows.length === 0){
      $tb.append('<tr><td colspan="6"><small>No hay trabajos listos para enviar a laboratorio.</small></td></tr>');
      return;
    }

    rows.forEach(r => {
      $tb.append(`
        <tr>
          <td><input type="checkbox" class="chk_lab" value="${r.id}"></td>
          <td>${r.folio}</td>
          <td>${r.cliente_nombre || ''}</td>
          <td>${money(r.total)}</td>
          <td>${money(r.pagado)}</td>
          <td>${r.fecha_venta ? r.fecha_venta : ''}</td>
        </tr>
      `);
    });
  }

  function cargarResumen(){
    const sucursal = $('#sucursal').val();
    const fecha = $('#fecha').val();

    if(!sucursal || !fecha) return;

    $.ajax({
      url: "<?=$url('ecom/'.$this->interfaz.'/getResumen')?>",
      method: "GET",
      dataType: "json",
      data: { sucursal, fecha },
      success: function(res){
        if(res.error){
          alertMessage(res.error);
          return;
        }

        // Caja y Depósito
        $('#fondo_caja').text(money(res.deposito.fondo_caja));
        $('#efectivo_dia').text(money(res.totales.efectivo || 0));
        $('#efectivo_disponible').text(money(res.deposito.efectivo_disponible));
        $('#tope_caja').text(money(res.deposito.tope_caja));
        $('#deposito_sugerido').text(money(res.deposito.deposito_sugerido));

        $('#total_dia').text(money(res.totales.total || 0));
        $('#t_efectivo').text(money(res.totales.efectivo || 0));
        $('#t_tarjeta').text(money(res.totales.tarjeta || 0));
        $('#t_tarjetac').text(money(res.totales.tarjetac || 0));
        $('#t_vales').text(money(res.totales.vales || 0));

        $('#deposito').val(res.deposito.deposito_sugerido || 0);


        // Tarjetas
        renderTarjetas(res.tarjetas);

        // Lab
        renderPendientesLab(res.pendientes_laboratorio);
      }
    });
  }

  $(document).ready(function(){
    // cuando cambie sucursal/fecha/fondo, recalcula
    $('#sucursal').on('change', cargarResumen);
    $('#fecha').on('change', cargarResumen);

    // check all
    $('#chk_all_lab').on('change', function(){
      $('.chk_lab').prop('checked', $(this).is(':checked'));
    });

    // enviar masivo a laboratorio
    $('#btn_enviar_lab').on('click', function(){
      const ids = $('.chk_lab:checked').map(function(){ return $(this).val(); }).get();
      if(ids.length === 0){
        alertMessage("Selecciona al menos un trabajo.");
        return;
      }

      $.ajax({
        url: "<?=$url('ecom/venta/enviarLaboratorioMasivo')?>",
        method: "POST",
        dataType: "json",
        data: { ids: ids },
        success: function(r){
          if(r.error){
            alertMessage(r.error);
            return;
          }
          alertMessage("Listo: enviados a laboratorio.", "success");
          cargarResumen(); // refresca lista
        }
      });
    });

    // carga inicial si ya trae fecha default
    cargarResumen();
  });



  /*––––––––––––––––––––––– /Producto fotos ––––––––––––––––––––––-––––––*/
</script>