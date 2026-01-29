<?php
$noImage = $urlm("images/image-holder.jpg");
$disenosNV = PublicidadPaqueteDisenoCR::model()->findAll("WHERE estatus = 1");
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
      <label class="col-lg-2 col-form-label">Nombre</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="nombre" placeholder="Nombre" value="<?=$data->nombre?>" required>
      </div>
    </div>

        
    <div class="form-group row">
      <label for="perfil" class="col-lg-2 col-form-label">Diseño</label>
      <div class="col-lg-10">
        <select class="form-control" id="diseno" name="diseno" required="required">
          <option value="">Selecciona una opción...</option>
          <?foreach ($disenosNV as $key => $value) {?>
          <option value="<?=$value->id;?>" <?=($value->id == $data->diseno) ? "selected":"";?>><?=$value->nombre;?></option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Descripción</label>
      <div class="col-lg-10">
        <textarea class="form-control" rows="3" name="descripcion"><?=$data->descripcion?></textarea>
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