<?php
$operador = new Operador("WHERE user = ".$this->user->id);
if($vendedor != ""){
  $operador = new Operador("WHERE user = ".$vendedor);
}

// Consulta para obtener los vendedores cuya columna "sucursal" contenga el ID de $sucursal
$vendedores = Operador::model()->findAll("WHERE FIND_IN_SET('$sucursal', sucursales)");

?>


<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/save")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Confirmar vendedor</h4>
  </div>

  <div class="modal-body">
      
    <input type="hidden" name="id" value="<?=$data->id?>" />


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Vendedor</label>
      <div class="col-lg-10">
        <select id="vendedor" name="vendedor" class="form-control" required>
          <option value="">Selecciona una opción...</option>
          <?foreach ($vendedores as $key => $value) {?>
          <option value="<?=$value->user?>" <?=($value->id == $vendedor) ? "selected":"";?>><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
          <?}?>
        </select>
      </div>
    </div>

      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <button type="button" id="btn-guardar-vendedor" class="btn btn-primary" onclick="seleccionarVendedor()">Seleccionar</button>
  </div>

 </form>

<script type="text/javascript">
  function seleccionarVendedor() {
    const vendedorId = $('#vendedor').val();
    const vendedorNombre = $('#vendedor option:selected').text();

    if (!vendedorId) {
      alertMessage("Por favor, selecciona un vendedor.", "warning");
      return;
    }

    // Devuelve el vendedor seleccionado al proceso principal
    const data = {
      id: vendedorId,
      nombre: vendedorNombre
    };

    window.parent.modalCallback(data); // Llama al callback definido en el proceso principal
    $('#myModal').modal('hide'); // Cierra la modal
  }
</script>