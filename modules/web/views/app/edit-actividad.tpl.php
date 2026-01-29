<?

$accion = "Guardar";
$noImage = $urlm("images/add-image.png");
$tipos = TicketTipo::model()->findAll();
$user = new User("WHERE app_token = '$token'");

$model = new Reclutador("WHERE user = ".$user->id);
$sWhere = "WHERE user != 1 AND (";
if($user->id != 1){
  $rPlazas = explode(",", $model->plazas);
  $rSep = "";
  if(count($rPlazas) == 0) $sWhere .= "1 = 1";
  foreach ($rPlazas as $key => $value) {
    $sWhere .= $rSep." FIND_IN_SET('".$value."', plazas) ";
    $rSep = " OR ";
  }
} else {
  $sWhere .= " 1 = 1 ";
}
$sWhere .= ")";

$rows = Reclutador::model()->findAll($sWhere);
$data = new Actividad($id);
$tipos = ActividadTipo::model()->findAll();
$aEstatus = ActividadEstatus::model()->findAll();

$imagenes = ActividadImagen::model()->findAll("WHERE actividad = ".$data->id);

$reclutador = new Reclutador("WHERE user = ".$data->asignado);

?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <link href="<?=$url("modules/ecom/css/plugins/datapicker/datepicker3.css");?>" rel="stylesheet">
    <!-- Toastr style -->
    <link href="<?=$url("modules/ecom/css/plugins/toastr/toastr.min.css");?>" rel="stylesheet">

    <script src="<?=$url("modules/ecom/js/jquery-2.1.1.js")?>"></script>
    <title>Observaciones</title>
  </head>
  <body>

    <div class="container" style="margin-bottom: 2em; padding-top: 2em;">

      <form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("web/app/saveactividad")?>">


        <input type="hidden" id="id" name="id" value="<?=$data->id?>" />
        <input type="hidden" id="registrante" name="registrante" value="<?=$user->id?>" />
        <input type="hidden" name="origen" value="app" />

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Asignado a</label>
          <div class="col-sm-10">
            <?if($data->id == ""){?>
            <select id="asignado" name="asignado" class="form-control">
              
            </select>
            <?} else {?>
            <input type="hidden" name="asignado" value="<?=$data->asignado;?>">
            <input type="text" class="form-control" value="<?=$reclutador->nombre." ".$reclutador->apaterno." ".$reclutador->amaterno;?>" disabled>
            <?}?>
          </div>
        </div> 

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Tipo</label>
          <div class="col-sm-10">
            <select name="tipo" class="form-control select2">
              <?php foreach ($tipos as $key => $value) {?>
              <option value="<?=$value->id?>"  <?=($value->id == $data->tipo) ? "selected":"";?> ><?=$value->nombre?></option>
              <?}?>
            </select>
          </div>
        </div>    
        
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Título</label>
          <div class="col-sm-10">
            <input type="text" placeholder="Título" name="nombre" maxlength="90" class="form-control" required="required" value="<?=$data->nombre?>">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Descripción</label>
          <div class="col-sm-10">
            <textarea name="descripcion" class="form-control summernote" rows="6"><?=$data->descripcion?></textarea>
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Fecha Inicio</label>
          <div class="col-sm-10">
            <input type="text" placeholder="Fecha de inicio" id="fecha_inicio" name="fecha_inicio" maxlength="13" class="form-control date" value="<?=$data->fecha_inicio?>" autocomplete="off">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Fecha tentativa</label>
          <div class="col-sm-10">
            <small>Fecha tentativa de finalización</small>
            <input type="text" placeholder="Fecha de finalización tentativa" name="fecha_fin_tentativa" maxlength="13" class="form-control date" value="<?=$data->fecha_fin_tentativa?>" autocomplete="off">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Fecha de finalización</label>
          <div class="col-sm-10">
            <input type="text" placeholder="Fecha de finalización" name="fecha_fin" maxlength="13" class="form-control date" value="<?=$data->fecha_fin?>" autocomplete="off">
          </div>
        </div>

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Estatus</label>
          <div class="col-sm-10">
            <select name="estatus" class="form-control select2">
              <?php foreach ($aEstatus as $key => $value) {?>
              <option value="<?=$value->id?>"  <?=($value->id == $data->estatus) ? "selected":"";?> ><?=$value->nombre?></option>
              <?}?>
            </select>
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
            <input type="file" name="imagenes[]" multiple="multiple">
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script src="<?=$url("modules/ecom/js/plugins/datapicker/bootstrap-datepicker.js");?>"></script>

    <script type="text/javascript">
      $(document).ready(function(){

          if($("#id").val() == ""){
            $('#asignado').select2({
              minimumInputLength: 3,
              placeholder: "Buscar reclutador...",
              dropdownAutoWidth : false,
              width: '100%',
              ajax: {
                url: '<?=$url("web/".$this->interfaz."/filtrareclutadores");?>',
                data: function (params) {
                  var query = {
                    search: params.term, 
                    id: '<?=$user->id?>'
                  }
                  // Query parameters will be ?search=[term]&type=public
                  return query;
                }
              }
            });
          }
        

          $(".summernote").summernote();

          $(".date").datepicker({
            showButtonPanel: true,
            format: 'yyyy-mm-dd'
          });

          $('#formEdit').submit(function() {
            console.log("submit");
            $("#btn-guardar").prop("disabled", true);
            $("#btn-guardar").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...`);
          });
          
          $('#formEdit').ajaxForm(function(response) {
            console.log(response);
            $("#btn-guardar").html("Guardar");
            if(response.error == undefined || response.error == ""){
              alertMessage("Actividad agregada", "success");

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