<?php
$noImage = $urlm("images/image-holder.jpg");
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
      <label class="col-lg-2 col-form-label">Nombre</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="nombre" value="<?=$data->nombre?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Clave</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="clave" value="<?=$data->clave?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Dirección</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="direccion" value="<?=$data->direccion?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Horario</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="horario" value="<?=$data->horario?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Teléfono</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="telefono" value="<?=$data->telefono?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Teléfono dudas</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="telefono_dudas" value="<?=$data->telefono_dudas?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Teléfono factura</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="telefono_factura" value="<?=$data->telefono_factura?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Correo</label>
      <div class="col-lg-10">
        <input type="email" class="form-control" name="correo" value="<?=$data->correo?>" required>
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