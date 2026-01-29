<?php
$noImage = $urlm("images/image-holder.jpg");
$clientes = Cliente::model()->findAll("WHERE estatus = 1");
$operadores = Operador::model()->findAll("WHERE id > 1 AND estatus = 1 AND admin = ".$this->user->id);
?>


<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/save")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Actualizar información</h4>
  </div>

  <div class="modal-body">
      
    <input type="hidden" name="id" value="<?=$data->id?>" />

    <div class="form-group row">
      <label class="col-sm-2 col-form-label">Imagen</label>
      <div class="col-sm-10">
        <img id="portada-img" src='<?=($data->portada != "") ? $url($data->portada): $noImage;?>?<?=time();?>' class="portada-foto"/>
        <input type="file" class="form-control" id="portada" name="portada" placeholder="" style="display: none;">
      </div>

    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Número de serie</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="no_serie" value="<?=$data->no_serie?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Cliente</label>
      <div class="col-lg-10">
        <select name="cliente" class="form-control" required>
          <option value="">Selecciona una opción...</option>
          <?foreach ($clientes as $key => $value) {?>
          <option value="<?=$value->id?>" <?=($value->id == $data->cliente) ? "selected":"";?>><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Operador</label>
      <div class="col-lg-10">
        <select name="operador" class="form-control" required>
          <option value="">Selecciona una opción...</option>
          <?foreach ($operadores as $key => $value) {?>
          <option value="<?=$value->id?>" <?=($value->id == $data->operador) ? "selected":"";?>><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Origen</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="origen" value="<?=$data->origen?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Destino</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="destino" value="<?=$data->destino?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Marca</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="marca" value="<?=$data->marca?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Modelo</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="modelo" value="<?=$data->modelo?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Año</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="anio" value="<?=$data->anio?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Placas</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="placas" value="<?=$data->placas?>">
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


  /*––––––––––––––––––––––– /Producto fotos ––––––––––––––––––––––-––––––*/
</script>