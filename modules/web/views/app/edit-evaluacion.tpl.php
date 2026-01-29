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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <link href="<?=$url("modules/ecom/css/plugins/datapicker/datepicker3.css");?>" rel="stylesheet">
    <!-- Toastr style -->
    <link href="<?=$url("modules/ecom/css/plugins/toastr/toastr.min.css");?>" rel="stylesheet">

    <script src="<?=$url("modules/ecom/js/jquery-2.1.1.js")?>"></script>
    <title>Observaciones</title>
  </head>
  <body style="background: #f8fafb;">
    <style type="text/css">
      .seccion-h3 {
        background: #303674;
        color: #ffffff;
        border-radius: 14px;
        padding: 7px;
        text-align: center;
    }
    </style>

    <div class="container" style="margin-bottom: 2em; padding-top: 2em;">

      <form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("web/app/saveEvaluacion")?>">


        <input type="hidden" name="user" value="<?=$user->id?>" />
        <input type="hidden" name="id" value="<?=$id?>" />

        <h3 class="seccion-h3">Entrevista en tienda</h3>
        <div class="form-group">
          <label class="col-form-label">¿El candidato se entrevistó en tienda?</label>
          <select class="form-control" name="entrevista_tienda" id="entrevista_tienda">
            <option value="0" selected="selected">No</option>
            <option value="1">Sí</option>
          </select>
        </div>

        <div class="form-group">
          <label class="col-form-label">De ser así ¿pasó la entrevista?</label>
          <select class="form-control" name="entrevista_tienda_aprobo">
            <option value="0" selected="selected">No</option>
            <option value="1">Sí</option>
          </select>
        </div>

        <h3 class="seccion-h3">REVISIÓN DE FUNCIONES</h3>
        <p class="small">
          Asignación de puntaje:
          1 = la respuesta es muy incompleta o insatisfactoria; 2 = la respuesta contiene algunos elementos correctos; 3 = la respuesta es completa y positiva.
        </p>

        <div class="form-group">
          <label class="col-form-label">¿Cuáles eran las tareas/actividades principales que realizabas en tu trabajo anterior?</label>
          <input type="text" name="pregunta_4_1" class="form-control" placeholder="Respuesta...">
        </div>
        <div class="form-group">
          <label class="col-form-label">¿Cuál de estas actividades era la que más te gustaba realizar? ¿Por qué?</label>
          <input type="text" name="pregunta_4_2" class="form-control" placeholder="Respuesta...">
        </div>
        <div class="form-group">
          <label class="col-form-label">De las actividades mencionadas, ¿Cuál seleccionarías para no realizar? ¿Por qué?</label>
          <input type="text" name="pregunta_4_3" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group row">
          <label class="col-3 col-form-label">Puntuación</label>
          <div class="col-sm-9">
            <input type="number" name="puntuacion_4" class="form-control puntuacion" placeholder="" max="3" min="0" maxlength="1" required="required">
          </div>
        </div>
        
        <hr>

        <h3 class="seccion-h3 mt-2">EXPERIENCIA LABORAL PREVIA (Se considera estabilidad 6 meses)</h3>
        <p class="small">
          Asignación de puntaje:
          1 = en todos los empleos estuvo menos de 6 meses; 2 = estabilidad en alguno; 3 = estable.
        </p>
        <div class="form-group row">
          <label class="col-3 col-form-label">Empresa</label>
          <label class="col-3 col-form-label">Duración en el puesto/$ sueldo</label>
          <label class="col-3 col-form-label">Actividades</label>
          <label class="col-3 col-form-label">Motivo de salida</label>
          <div class="col-3"><input type="text" name="pregunta_5_1_1" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_1_2" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_1_3" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_1_4" class="form-control" placeholder="Respuesta..."></div>
          
          <div class="col-3"><input type="text" name="pregunta_5_2_1" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_2_2" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_2_3" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_2_4" class="form-control" placeholder="Respuesta..."></div>
          
          <div class="col-3"><input type="text" name="pregunta_5_3_1" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_3_2" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_3_3" class="form-control" placeholder="Respuesta..."></div>
          <div class="col-3"><input type="text" name="pregunta_5_3_4" class="form-control" placeholder="Respuesta..."></div>
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Qué es lo que más te gustaba de tu trabajo anterior?</label>
          <input type="text" name="pregunta_5_4" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Qué es lo que menos te gustaba?</label>
          <input type="text" name="pregunta_5_5" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Tienes dependientes económicos? ¿Quienes?</label>
          <input type="text" name="pregunta_5_6" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Tienes deudas? Monto:</label>
          <input type="text" name="pregunta_5_7" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Cuentas con crédito INFONAVIT? Monto:</label>
          <input type="text" name="pregunta_5_8" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Cuentas con FONACOT? Monto:</label>
          <input type="text" name="pregunta_5_9" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Pagas renta / Vives en casa propia? Monto:</label>
          <input type="text" name="pregunta_5_10" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Qué otros gastos mensuales fijos tienes? Monto:</label>
          <input type="text" name="pregunta_5_11" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">Tiempo de residencia en domicilio actual:</label>
          <input type="text" name="pregunta_5_12" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Cuentas con algún adeudo en banco? ¿Qué banco?</label>
          <input type="text" name="pregunta_5_13" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group">
          <label class="col-form-label">Ingreso mensual actual/deseado(si no trabaja)</label>
          <input type="text" name="pregunta_5_14" class="form-control" placeholder="Respuesta...">
        </div>

        <div class="form-group row">
          <label class="col-3 col-form-label">Puntuación</label>
          <div class="col-sm-9">
            <input type="number" name="puntuacion_5" class="form-control puntuacion" placeholder="" max="3" min="0" maxlength="1" required="required">
          </div>
        </div>

        <hr>

        <h3 class="seccion-h3 mt-2">TRABAJO SOBRE PRESIÓN</h3>
        <p class="small">
          Asignación de puntaje:
          1 = respuesta pobre; 2 = regular; 3 = buena.
        </p>
        <div class="form-group">
          <label class="col-form-label">Cuéntame la situación laboral más tensa que tuviste que resolver en tu último trabajo.</label>
          <input type="text" name="pregunta_6_1" class="form-control" placeholder="Respuesta...">
        </div>
        <div class="form-group row">
          <label class="col-3 col-form-label">Puntuación</label>
          <div class="col-sm-9">
            <input type="number" name="puntuacion_6" class="form-control puntuacion" placeholder="" max="3" min="1" maxlength="1" required="required">
          </div>
        </div>

        <hr>

        <h3 class="seccion-h3 mt-2">TRABAJO EN EQUIPO</h3>
        <p class="small">
          Asignación de puntaje:
          1 = respuesta pobre; 2 = regular; 3 = buena.
        </p>
        <div class="form-group">
          <label class="col-form-label">Dame un ejemplo específico de una tarea que tuvo que realizar en equipo, en su último trabajo ¿Cuál fue su aportación al equipo?</label>
          <input type="text" name="pregunta_7_1" class="form-control" placeholder="Respuesta...">
        </div>
        <div class="form-group row">
          <label class="col-3 col-form-label">Puntuación</label>
          <div class="col-sm-9">
            <input type="number" name="puntuacion_7" class="form-control puntuacion" placeholder="" max="3" min="1" maxlength="1" required="required">
          </div>
        </div>

        <hr>

        <h3 class="seccion-h3 mt-2">SERVICIO AL CLIENTE</h3>
        <p class="small">
          Asignación de puntaje:
          1 = respuesta pobre; 2 = regular; 3 = buena.
        </p>
        <div class="form-group">
          <label class="col-form-label">¿Qué recomendarías a un empleado de OXXO para que mejorara su servicio?</label>
          <input type="text" name="pregunta_8_1" class="form-control" placeholder="Respuesta...">
        </div>
        <div class="form-group row">
          <label class="col-3 col-form-label">Puntuación</label>
          <div class="col-sm-9">
            <input type="number" name="puntuacion_8" class="form-control puntuacion" placeholder="" max="3" min="1" maxlength="1" required="required">
          </div>
        </div>

        <h3 class="seccion-h3 mt-2">COMPETENCIAS</h3>
        <p class="small">
          Explica que realizarás preguntas específicas sobre experiencias pasadas y que estarás esperando respuestas concretas con ejemplos.<br>
          Califica en base al nivel del 1 al 5 donde el: 1. Poco observado  2. Básico  3. En desarrollo  4. Avanzado  5.  Experto modelo a seguir.
        </p>


        <div class="form-group row">
          <div class="col-12"><label class="col-form-label">Platícame de la mejor y la peor experiencia que hayas tenido dándole servicio a un cliente. ¿Qué sucedió? Si no ha trabajado pregúntale sobre tareas en su casa o escuela</label></div>
          <label class="col-3 col-form-label">Situación</label>
          <label class="col-3 col-form-label">Acción</label>
          <label class="col-3 col-form-label">Resultado</label>
          <label class="col-3 col-form-label">Evaluación</label>
          <div class="col-3"><textarea name="pregunta_9_1_1" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_1_2" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_1_3" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_1_4" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
        </div>

        <div class="form-group row">
          <div class="col-12"><label class="col-form-label">Mencióname una situación en donde realizaste muchas actividades ¿Cómo lograste los objetivos?</label></div>
          <label class="col-3 col-form-label">Situación</label>
          <label class="col-3 col-form-label">Acción</label>
          <label class="col-3 col-form-label">Resultado</label>
          <label class="col-3 col-form-label">Evaluación</label>
          <div class="col-3"><textarea name="pregunta_9_2_1" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_2_2" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_2_3" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_2_4" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
        </div>

        <div class="form-group row">
          <div class="col-12"><label class="col-form-label">Platícame de alguna ocasión en la que te haya tocado ver o saber que alguno de tus compañeros estaba tomando algo que no era suyo. ¿Qué hiciste?</label></div>
          <label class="col-3 col-form-label">Situación</label>
          <label class="col-3 col-form-label">Acción</label>
          <label class="col-3 col-form-label">Resultado</label>
          <label class="col-3 col-form-label">Evaluación</label>
          <div class="col-3"><textarea name="pregunta_9_3_1" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_3_2" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_3_3" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
          <div class="col-3"><textarea name="pregunta_9_3_4" class="form-control" placeholder="Respuesta..." rows="3"></textarea></div>
        </div>

        <div class="form-group">
          <label class="col-form-label">¿Por qué te gustaría trabajar en OXXO?</label>
          <input type="text" name="pregunta_9_4" class="form-control" placeholder="Respuesta...">
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

          $('#formEdit').submit(function() {
            console.log("submit");
            $("#btn-guardar").prop("disabled", true);
            $("#btn-guardar").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...`);
          });
          
          $('#formEdit').ajaxForm(function(response) {
            console.log(response);
            $("#btn-guardar").html("Guardar");
            if(response.error == undefined || response.error == ""){
              alertMessage("Evaluación guardada", "success");

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