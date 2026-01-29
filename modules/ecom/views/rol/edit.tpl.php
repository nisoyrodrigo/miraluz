<?php
$rol = new Rol($id);
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
  <i class="fa fa-pencil modal-icon"></i>
  <h4 class="modal-title">Actualizar información</h4>
</div>

<div class="modal-body">
  
  <form id="formEdit">

    <input type="hidden" name="id" value="<?=$rol->id?>" />
    
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Nombre</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Nombre" name="name" maxlength="90" class="form-control" required="required" value="<?=$rol->name?>">
      </div>
    </div>
    
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Creado</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Fecha de creación" name="created" maxlength="60" class="form-control" value="<?=$rol->created?>" disabled>
      </div>
    </div>
    
    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Modificado</label>
      <div class="col-sm-10">
        <input type="text" placeholder="Fecha de modificacion" name="modified" maxlength="60" class="form-control" value="<?=$rol->modified?>" disabled>
      </div>
    </div>
  </form>

</div>

<div class="modal-footer">
  <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
  <button id="btn-guardar" type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Guardando">Guardar</button>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $("#btn-guardar").click(function(){
      var $this = $(this);
      $this.button('loading');
      enviaFormularioModel("formEdit", "<?=$url("ecom/rol/save")?>", [], hDefault);
    });
  });
</script>