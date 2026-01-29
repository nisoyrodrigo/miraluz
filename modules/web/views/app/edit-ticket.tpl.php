<?

$accion = "Guardar";
$noImage = $urlm("images/add-image.png");
$tipos = TicketTipo::model()->findAll();

$user = new User("WHERE app_token = '$token'");

?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">



    <!-- Toastr style -->
    <link href="<?=$url("modules/ecom/css/plugins/toastr/toastr.min.css");?>" rel="stylesheet">

    <script src="<?=$url("modules/ecom/js/jquery-2.1.1.js")?>"></script>
    <title>Observaciones</title>
  </head>
  <body>

    <div class="container" style="margin-bottom: 2em; padding-top: 2em;">

      <form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("web/app/saveticket")?>">


        <input type="hidden" name="usuario" value="<?=$user->id?>" />
        <input type="hidden" name="origen" value="app" />

        <div class="form-group row">
          <div class="col-md-12">
            <label for="nombre">Título</label>
            <input type="text" class="form-control" name="titulo" placeholder="Título" value="<?=$data->titulo?>" required="required">
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="tipo">Tipo</label>
            <select class="form-control" name="tipo" required="required">
              <option value="">Selecciona un tipo</option>
              <?foreach ($tipos as $key => $value) {?>
              <option value="<?=$value->id?>"><?=$value->nombre?></option>
             <?}?>
            </select>
          </div>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="contenido">Descripción</label>
            <textarea name="contenido" class="form-control summernote" rows="6" required="required"></textarea>
          </div>
        </div>

        <div class="form-group row">
          <?foreach ($imagenes as $key => $value) {?>
          <div id="imagen_<?=$value->id?>"class="col-md-2 ticket-img">
            <a href="<?=$url($value->imagen);?>" target="_blank"><img src="<?=$url($value->imagen);?>"></a>
            <a href="javascript:void(0);" class="delete-image" onclick="borraImagen(<?=$value->id?>);"><i class="fas fa-trash"></i></a>
          </div>
          <?}?>
        </div>

        <div class="form-group row">
          <div class="col-md-12">
            <label for="imagenes">Adjuntar imágenes</label>
            <input type="file" name="imagenes[]" multiple="multiple" accept="image/*">
          </div>
        </div>

        <div class="form-group">
          <button id="btn-guardar" type="submit" class="btn btn-primary w-100" style="background: #E1251B; border-color: #E1251B;">Guardar</button>
        </div>

      </form>

    </div>



    <script src="<?=$url("modules/ecom/js/plugins/jqueryform/jquery.form.js");?>"></script>

    <!-- Toastr -->
    <script src="<?=$url("modules/ecom/js/plugins/toastr/toastr.min.js");?>"></script>
    <!-- funciones -->
    <script src="<?=$url("modules/ecom/js/funciones.js");?>"></script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

    <script type="text/javascript">
      $(document).ready(function(){


          $('#formEdit').submit(function() {
            console.log("submit");
            $("#btn-guardar").prop("disabled", true);
            $("#btn-guardar").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...`);
          });
          
          $('#formEdit').ajaxForm(function(response) {
            console.log(response);
            $("#btn-guardar").html("Guardar");
            if(response.error == undefined || response.error == ""){
              alertMessage("Ticket agregado", "success");

              window.flutter_inappwebview.callHandler('cierraVentana', 12, 2, 50).then(function(result) {
               // get result from Flutter side. It will be the number 64.
               console.log(result);
             });
            } else {
              alertMessage(response.error);
              console.log("Error al guardar");
            }
          });

        });


        /*––––––––––––––––––––––– Distintivo ––––––––––––––––––––––-––––––*/
  $(".portada-foto").click(function(){$("#portada").click();});
  /*––––––––––––––––––––––– /Distintivo ––––––––––––––––––––––-––––––*/
  $("#portada").change(function() {
    var input = $(this)[0];
    var url = $(this).val();
    var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
    if (input.files && input.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg")){
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

  function borraImagen(id){
    $.post("<?=$url("ecom/".$this->interfaz."/eliminaImagen")?>", {"id":id}, function (response){
      if(response.error == undefined){
        alertMessage("Imagen eliminada satisfactoriamente.", "success");
        $("#imagen_" + id).remove();
      } else {
        alertMessage(response.error);
        console.log("Error al eliminar imagen");
      }
    });
  }
    </script>

  </body>
</html>