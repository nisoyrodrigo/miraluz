<?
$user = new User($data->user);
$roles = Rol::model()->findAll("WHERE id > 2");
$sucursales = Sucursal::model()->findAll("WHERE estatus = 1");
$sucursalesSeleccionadas = explode(',', $data->sucursales);
?>
<style type="text/css">
  .select2-container{z-index: 9999999}
</style>
<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/save")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Actualizar información</h4>
  </div>

  <div class="modal-body">
    
    <input type="hidden" id="reclutador" name="id" value="<?=$data->id?>" />
    <input type="hidden" name="user" value="<?=$data->user?>" />
    <?if($this->user->id == "1" || $this->user->id == "5"){?>
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Rol</label>
        <div class="col-sm-10">
          <select class="form-control" name="rol">
            <option value="">Selecciona un rol.</option>
            <option value="2" <?=$user->rol == "2" ? "selected":"";?>>Administrador</option>
            <option value="3" <?=$user->rol == "3" ? "selected":"";?>>Gerente</option>
            <option value="4" <?=$user->rol == "4" ? "selected":"";?>>Vendedor</option>
          </select>
        </div>
      </div>
    <?} else {?>
      <input type="hidden" name="rol" value="<?=($user->rol != "") ? $user->rol: "3";?>" />
    <?}?>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Nombre</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Nombre" name="nombre" maxlength="90" class="form-control" required="required" value="<?=$data->nombre?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">A. paterno</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Apellido paterno" name="apaterno" maxlength="90" class="form-control" required="required" value="<?=$data->apaterno?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">A. materno</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Apellido materno" name="amaterno" maxlength="90" class="form-control" value="<?=$data->amaterno?>">
      </div>
    </div>

    <!-- Campo Select de Sucursales -->
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Sucursales</label>
      <div class="col-sm-10">
        <select class="form-control select2 w100" name="sucursales[]" multiple="multiple">
          <?foreach($sucursales as $sucursal) {?>
            <option value="<?=$sucursal->id?>" <?= in_array($sucursal->id, $sucursalesSeleccionadas) ? 'selected' : ''; ?>>
              <?=$sucursal->nombre?>
            </option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">¿Es Optometrista?</label>
      <div class="col-sm-10">
        <input type="checkbox" name="optometrista" value="1" <?= $data->optometrista == "1" ? "checked" : "";?>>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Correo</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Correo" name="correo" maxlength="90" class="form-control" required="required" value="<?=$user->username;?>" <?=($data->id != "") ? "disabled":"";?> autocomplete="off">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Password</label>
      <div class="col-sm-10">
        <input type="password" placeholder="Password" name="password" maxlength="90" class="form-control" value="" autocomplete="new-password">
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

    $('.select2').select2();

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
        $('#tabla-data').DataTable().ajax.reload();
      } else {
        alertMessage(response.error);
        console.log("Error al guardar");
      }
    });

  });


</script>