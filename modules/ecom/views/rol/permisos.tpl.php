<?php
$rol = new Rol($id);
$query  = "SELECT s.id, s.name, s.action, s.status, us.permiso FROM ";
$query .= "section s LEFT JOIN ";
$query .= "user_section us ON us.section = s.id AND us.rol = ".$rol->id." AND user IS NULL ORDER BY name ASC, action ASC";
$sections = Section::model()->executeQuery($query);

$aSecciones = array();
foreach ($sections as $key => $value):
  $aSecciones[$value->name]["accion"][] = $value;
endforeach;

?>
<style type="text/css">

</style>
<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/rol/savePermisos")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Actualizar permisos para: <?=$rol->name?></h4>
  </div>

  <div class="modal-body">
      
      <input type="hidden" name="id" value="<?=$rol->id?>" />
      <div class="tabs-container">
        <div class="tabs-left">
          <ul class="nav nav-tabs">
            <?
            $tabClass="active";
            foreach ($aSecciones as $key => $value):
            ?>
            <li class="<?=$tabClass?>"><a data-toggle="tab" href="#tab-<?=$key?>"><?=$key?></a></li>
            <?
            $tabClass = "";
            endforeach;?>
          </ul>
          <div class="tab-content">
            <?
            $tabClass = "active";
            foreach ($aSecciones as $key => $value):?>
            <div id="tab-<?=$key?>" class="tab-pane <?=$tabClass?>">
              <div class="panel-body">
              <?foreach ($value["accion"] as $skey => $action):
                
              ?>
                <div class="col-sm-6">
                  <div class="checkbox checkbox-info checkbox-circle tooltip-demo">
                    <input id="seccion_<?=$action->id?>" name="seccion[<?=$action->id?>]" type="checkbox" value="1" class="i-checks" <?=($action->permiso == "1") ? "checked":"";?>>
                    <label class="label-permiso" for="seccion_<?=$action->id?>" style="color: #000 !important;" data-toggle="tooltip" data-placement="right">
                      <?=$action->action?>
                    </label>
                  </div>
                </div>
              <?endforeach;?>
              </div>
            </div>
            <?
            $tabClass = "";
            endforeach;?>
          </div>
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
    
    $('#formEdit').submit(function() {
      console.log("submit");
      $("#btn-guardar").button('loading');
    });
    
    $('#formEdit').ajaxForm(function(response) {
      console.log(response);
      $("#btn-guardar").button('reset');
      if(response.error == undefined || response.error == ""){
        alertMessage("Candidato enviado a cartera", "success");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();
      } else {
        alertMessage(response.error);
        console.log("Error al guardar");
      }
    });

  });

  $('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-green',
    radioClass: 'iradio_square-green'
  });
</script>