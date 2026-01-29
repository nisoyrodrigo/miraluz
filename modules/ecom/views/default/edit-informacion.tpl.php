<?
$data = new Operador("WHERE user = ".$this->user->id);
?>
<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/saveInformacion")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Hola <?=$data->nombre;?></h4>
  </div>

  <div class="modal-body">
    
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Password actual<small>(max 10 caracteres)</small></label>
      <div class="col-sm-10">
        <input type="password" placeholder="Password actual" name="password_actual" maxlength="10" class="form-control" value="">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Nuevo password<small>(max 10 caracteres)</small></label>
      <div class="col-sm-10">
        <input type="password" placeholder="Nuevo password" name="password_nuevo" maxlength="10" class="form-control" value="">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Confirma nuevo password<small>(max 10 caracteres)</small></label>
      <div class="col-sm-10">
        <input type="password" placeholder="Confirma nuevo password" name="password_nuevo_confirma" maxlength="10" class="form-control" value="">
      </div>
    </div>

  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button id="btn-guardar" type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Guardando">Guardar</button>
  </div>

</form>

<script type="text/javascript">
  $(document).ready(function(){

    $('#formEdit').submit(function() {
      console.log("submit");
      $("#btn-guardar").button('loading');
    });
    
    $('#formEdit').ajaxForm(function(response) {
      console.log(response);
      $("#btn-guardar").button('reset');
      if(response.error == undefined || response.error == ""){
        alertMessage("Información guardada satisfactoriamente.", "success");
        hideModal();
      } else {
        alertMessage(response.error);
        console.log("Error al guardar");
      }
    });

  });


</script>