<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title">Datos para Receta - Folio: <?=$data->folio ?? $data->id?></h4>
</div>

<div class="modal-body">
  <form id="form-receta" class="form-horizontal">
    <input type="hidden" name="id" value="<?=$data->id?>">

    <!-- AGUDEZA VISUAL -->
    <div class="panel panel-primary">
      <div class="panel-heading">
        <h5 class="panel-title">Agudeza Visual</h5>
      </div>
      <div class="panel-body">
        <table class="table table-bordered table-sm">
          <thead class="thead-light">
            <tr>
              <th>Ojo</th>
              <th>Con Lentes</th>
              <th>Sin Lentes</th>
              <th>Con Correccion</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>OD</strong></td>
              <td><input type="text" name="od_av_con_lentes" class="form-control input-sm" value="<?=$data->od_av_con_lentes?>" placeholder="Ej: 20/20"></td>
              <td><input type="text" name="od_av_sin_lentes" class="form-control input-sm" value="<?=$data->od_av_sin_lentes?>" placeholder="Ej: 20/40"></td>
              <td><input type="text" name="od_av_con_correccion" class="form-control input-sm" value="<?=$data->od_av_con_correccion?>" placeholder="Ej: 20/20"></td>
            </tr>
            <tr>
              <td><strong>OI</strong></td>
              <td><input type="text" name="oi_av_con_lentes" class="form-control input-sm" value="<?=$data->oi_av_con_lentes?>" placeholder="Ej: 20/20"></td>
              <td><input type="text" name="oi_av_sin_lentes" class="form-control input-sm" value="<?=$data->oi_av_sin_lentes?>" placeholder="Ej: 20/40"></td>
              <td><input type="text" name="oi_av_con_correccion" class="form-control input-sm" value="<?=$data->oi_av_con_correccion?>" placeholder="Ej: 20/20"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- DIAGNOSTICO VISUAL (Padecimientos) -->
    <div class="panel panel-info">
      <div class="panel-heading">
        <h5 class="panel-title">Padecimientos</h5>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4">
            <label class="checkbox-inline">
              <input type="checkbox" name="tiene_miopia" value="1" <?=$data->tiene_miopia == '1' ? 'checked' : ''?>> Miopia
            </label>
          </div>
          <div class="col-md-4">
            <label class="checkbox-inline">
              <input type="checkbox" name="tiene_hipermetropia" value="1" <?=$data->tiene_hipermetropia == '1' ? 'checked' : ''?>> Hipermetropia
            </label>
          </div>
          <div class="col-md-4">
            <label class="checkbox-inline">
              <input type="checkbox" name="tiene_astigmatismo" value="1" <?=$data->tiene_astigmatismo == '1' ? 'checked' : ''?>> Astigmatismo
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- OBSERVACIONES -->
    <div class="form-group">
      <label class="col-sm-2 control-label">Observaciones</label>
      <div class="col-sm-10">
        <textarea name="observaciones" class="form-control" rows="2" placeholder="Observaciones generales..."><?=$data->observaciones?></textarea>
      </div>
    </div>

    <!-- DIAGNOSTICO -->
    <div class="form-group">
      <label class="col-sm-2 control-label">Diagnostico</label>
      <div class="col-sm-10">
        <textarea name="diagnostico" class="form-control" rows="2" placeholder="Diagnostico del paciente..."><?=$data->diagnostico?></textarea>
      </div>
    </div>

    <!-- TRATAMIENTO -->
    <div class="form-group">
      <label class="col-sm-2 control-label">Tratamiento</label>
      <div class="col-sm-10">
        <textarea name="tratamiento" class="form-control" rows="2" placeholder="Tratamiento recomendado..."><?=$data->tratamiento?></textarea>
      </div>
    </div>

    <!-- PROXIMA CITA -->
    <div class="form-group">
      <label class="col-sm-2 control-label">Proxima Cita</label>
      <div class="col-sm-4">
        <input type="date" name="proxima_cita" class="form-control" value="<?=$data->proxima_cita ? date('Y-m-d', strtotime($data->proxima_cita)) : date('Y-m-d', strtotime('+1 year'))?>">
      </div>
    </div>

  </form>
</div>

<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
  <button type="button" class="btn btn-primary" id="btn-guardar-receta">
    <i class="fas fa-save"></i> Guardar e Imprimir Receta
  </button>
</div>

<script>
$(document).ready(function(){

  $('#btn-guardar-receta').click(function(){
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

    var formData = {
      id: $('input[name="id"]').val(),
      od_av_con_lentes: $('input[name="od_av_con_lentes"]').val(),
      od_av_sin_lentes: $('input[name="od_av_sin_lentes"]').val(),
      od_av_con_correccion: $('input[name="od_av_con_correccion"]').val(),
      oi_av_con_lentes: $('input[name="oi_av_con_lentes"]').val(),
      oi_av_sin_lentes: $('input[name="oi_av_sin_lentes"]').val(),
      oi_av_con_correccion: $('input[name="oi_av_con_correccion"]').val(),
      tiene_miopia: $('input[name="tiene_miopia"]').is(':checked') ? 1 : 0,
      tiene_hipermetropia: $('input[name="tiene_hipermetropia"]').is(':checked') ? 1 : 0,
      tiene_astigmatismo: $('input[name="tiene_astigmatismo"]').is(':checked') ? 1 : 0,
      observaciones: $('textarea[name="observaciones"]').val(),
      diagnostico: $('textarea[name="diagnostico"]').val(),
      tratamiento: $('textarea[name="tratamiento"]').val(),
      proxima_cita: $('input[name="proxima_cita"]').val()
    };

    $.post('<?=$url("ecom/venta/saveReceta")?>', formData, function(response){
      if(response.error){
        toastr.error(response.error);
        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar e Imprimir Receta');
      } else {
        toastr.success('Datos guardados correctamente');
        // Cerrar modal y abrir receta
        $('.modal').modal('hide');
        var url = '<?=$url("ecom/venta/imprimeReceta")?>?id=' + formData.id;
        window.open(url, '_blank');
      }
    }, 'json').fail(function(){
      toastr.error('Error al guardar');
      btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar e Imprimir Receta');
    });
  });

});
</script>
