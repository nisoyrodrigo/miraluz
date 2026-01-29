<?php
$noImage = $urlm("images/image-holder.jpg");
$sucursal = new Sucursal($data->sucursal);
?>


<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/save")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Corte del día (<?=date('d/m/Y', strtotime($data->fecha));?>) <?=$sucursal->nombre?></h4>
  </div>

  <div class="modal-body">
      
    <input type="hidden" name="id" value="<?=$data->id?>" />

    <div class="table-responsive">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Forma de pago</th>
            <th>Monto capturado</th>
            <th>Monto ventas</th>
            <th>Diferencia</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Efectivo</td>
            <td><?=$data->efectivo_contado?></td>
            <td><?=$data->efectivo_ingreso?></td>
            <td><?=($data->efectivo_contado - $data->efectivo_ingreso)?></td>
          </tr>
          <tr>
            <td>Tarjeta débito</td>
            <td><?=$data->tarjeta_contado?></td>
            <td><?=$data->tarjeta_ingreso?></td>
            <td><?=($data->tarjeta_contado - $data->tarjeta_ingreso)?></td>
          </tr>
          <tr>
            <td>Tarjeta crédito</td>
            <td><?=$data->tarjetac_contado?></td>
            <td><?=$data->tarjetac_ingreso?></td>
            <td><?=($data->tarjetac_contado - $data->tarjetac_ingreso)?></td>
          </tr>
          <tr>
            <td>Vales</td>
            <td><?=$data->vales_contado?></td>
            <td><?=$data->vales_ingreso?></td>
            <td><?=($data->vales_contado - $data->vales_ingreso)?></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th>
              <?php 
              $total_contado = $data->efectivo_contado + $data->tarjeta_contado + $data->tarjetac_contado + $data->vales_contado;
              echo $total_contado;
              ?>
            </th>
            <th>
              <?php 
              $total_ingreso = $data->efectivo_ingreso + $data->tarjeta_ingreso + $data->tarjetac_ingreso + $data->vales_ingreso;
              echo $total_ingreso;
              ?>
            </th>
            <th>
              <?php 
              $total_diferencia = ($data->efectivo_contado - $data->efectivo_ingreso) +
                                  ($data->tarjeta_contado - $data->tarjeta_ingreso) +
                                  ($data->tarjetac_contado - $data->tarjetac_ingreso) +
                                  ($data->vales_contado - $data->vales_ingreso);
              echo $total_diferencia;
              ?>
            </th>
          </tr>
        </tfoot>
      </table>
    </div>
      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
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