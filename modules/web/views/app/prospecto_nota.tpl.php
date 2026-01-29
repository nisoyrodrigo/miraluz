<?

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

    <div class="container" style="margin-bottom: 2em;">

      <form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("web/app/saveprospectonota")?>">


        <input type="hidden" name="id" value="<?=$data->id?>" />

        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Observaciones</label>
          <div class="col-sm-10">
            <textarea name="observaciones" class="form-control" rows="10" required="required"><?=$data->observaciones;?></textarea>
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
            $("#btn-guardar").button('loading');
          });
          
          $('#formEdit').ajaxForm(function(response) {
            console.log(response);
            $("#btn-guardar").button('reset');
            if(response.error == undefined || response.error == ""){
              alertMessage("Observaciones actualizadas", "success");
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
    </script>

  </body>
</html>