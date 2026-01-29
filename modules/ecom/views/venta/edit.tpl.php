<?php
$noImage = $urlm("images/image-holder.jpg");
$clientes = Cliente::model()->findAll("WHERE estatus = 1");
$tipos = TipoProducto::model()->findAll("WHERE estatus = 1");
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
      <label class="col-lg-2 col-form-label">Cliente</label>
      <div class="col-lg-10">
        <select class="form-control select2 w-100" name="cliente">
          <option value="">Selecciona un cliente</option>
          <?foreach ($clientes as $key => $value) {?>
          <option value="<?=$value->id?>"><?=$value->nombre?> - <?=$value->correo?> - <?=$value->telefono?></option>
          <?}?>
        </select>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Molestias</label>
      <div class="col-lg-10">
        <textarea class="form-control" rows="2"><?=$data->molestias?></textarea>
      </div>
    </div>

    <div class="row">
      <div class="col-6">
        <div class="form-group row">
          <label class="col-lg-2 col-form-label">Último RX</label>
          <div class="col-lg-10">
            <input type="text" class="form-control" name="ultimo_rx" value="<?=$data->ultimo_rx?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-lg-2 col-form-label">Patologías</label>
          <div class="col-lg-10">
            <input type="text" class="form-control" name="patologias" value="<?=$data->patologias?>">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="form-group row">
          <label class="col-lg-2 col-form-label">DIP</label>
          <div class="col-lg-10">
            <input type="text" class="form-control" name="dip" value="<?=$data->dip?>">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-lg-2 col-form-label">Patologías oculares</label>
          <div class="col-lg-10">
            <input type="text" class="form-control" name="patologias_oculares" value="<?=$data->patologias_oculares?>">
          </div>
        </div>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Precio público</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" name="precio_publico" value="<?=$data->precio_publico?>" required>
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