<?
$estado = new Estado($data->dir_estado);

$horarios = array(
  "ves" => "Vespertino",
  "mat" => "Matutino",
  "todos" => "Todos",
);


$escolaridad = array(
  "1" => "Primaria",
  "2" => "Secundaria",
  "3" => "Bachillerato",
  "4" => "Licenciatura",
);
?>
<!doctype html>
<html lang="es">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Ficha</title>
  </head>
  <body>

    <div class="container" style="margin-bottom: 2em;">

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Nombre</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Nombre" name="nombre" maxlength="90" class="form-control" required="required" value="<?=$data->nombre?>" readonly>
        </div>
      </div>


      <div class="form-group row">
        <label class="col-sm-2 col-form-label">A. paterno</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Apellido paterno" name="apaterno" maxlength="90" class="form-control" required="required" value="<?=$data->apaterno?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">A. materno</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Nombre" name="amaterno" maxlength="90" class="form-control" required="required" value="<?=$data->amaterno?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Fecha de nacimiento</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Fecha de nacimiento" name="fecha_nacimiento" maxlength="90" class="form-control" required="required" value="<?=$data->fecha_nacimiento?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Código postal</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Código postal" maxlength="90" class="form-control" required="required" value="<?=$data->dir_cp?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Estado</label>
        <div class="col-sm-10">
          <input type="text" placeholder="Código postal" maxlength="90" class="form-control" required="required" value="<?=$estado->nombre?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Municipio</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->dir_municipio?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Colonia</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->dir_colonia?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Domicilio</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->dir_domicilio?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Teléfono</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->telefono?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Teléfono adicional</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->telefono_adicional?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Correo</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->correo?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Estado civil</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->estado_civil?>" readonly>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Cuenta con identificación oficial</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=($data->tiene_identificacion == 1) ? "Sí":"No";?>" readonly>
        </div>
      </div>

      
      <div class="form-group row">
        <label class="col-sm-2 col-form-label">¿Es reingreso?</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=($data->antecedente_oxxo == 1) ? "Sí":"No";?>" readonly>
        </div>
      </div>


      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Motivo Salida</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->antecedente_oxxo_detalle?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Disponibilidad rolar turno</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=($data->disponibilidad_rolar_turno == 1) ? "Sí":"No";?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Disponibilidad trabajo fin de semana</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=($data->disponibilidad_fin_semana == 1) ? "Sí":"No";?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Puede presentarse</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$data->fecha_inicio_labores?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Escolaridad</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" required="required" value="<?=$escolaridad[$data->dato_escolaridad]?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Puesto solicitado</label>
        <div class="col-sm-10">
          <input type="text" maxlength="90" class="form-control" value="<?=$data->puesto_solicitado?>" readonly>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-2 col-form-label">Comentarios</label>
        <div class="col-sm-10">
          <textarea class="form-control" readonly rows="5"><?=$data->comentarios;?></textarea>
        </div>
      </div>

    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>