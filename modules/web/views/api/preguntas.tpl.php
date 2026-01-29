<?
$preguntas = PreguntaQS::model()->findAll("WHERE estatus = 1 AND puesto = '$puesto' ORDER BY orden ASC");

foreach ($preguntas as $key => $value) {
  $respuestas = PreguntaQSOpcion::model()->findAll("WHERE pregunta = ".$value->id);
?>

  <fieldset class="form-group">
    <div class="row">
      <legend class="col-form-label col-sm-12 pt-0"><?=$value->pregunta?></legend>
      <div class="col-sm-12">
        <?
        foreach ($respuestas as $skey => $svalue) {?>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="pregunta_<?=$value->id?>" value="<?=$svalue->id;?>">
            <label class="form-check-label" for="pregunta_<?=$value->id?>">
              <?=$svalue->respuesta?>
            </label>
          </div>
        <?}?>
      </div>
    </div>
  </fieldset>
  <hr>


<?}?>