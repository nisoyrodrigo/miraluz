<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Servicio <?=$data->no_serie?></h2>
    <ol class="breadcrumb">
      
    </ol>
    <h5>Origen: <?=$data->origen?></h5>
    <h5>Destino: <?=$data->destino?></h5>
  </div>
  <div class="col-lg-2 pt-2">
    <a class="btn btn-primary" style="color:#FFF;" href="<?=$url("ecom/servicio/");?>">Volver</a>
  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-content">
          <div class="table-responsive">
            <table id="tabla-data" class="table table-striped table-hover data-table">
              <thead>
                <tr>
                  <th></th>
                  <th>Tipo</th>
                  <th></th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>

          <div class="col-lg-12 pt-2 text-right">
            <a class="btn btn-primary" style="color:#FFF;" onclick="finalizaRecoleccion(<?=$data->id?>)">Finalizar recolección</a>
          </div>

        </div>
      
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  
  $(document).ready(function() {

    var tableData = $('#tabla-data').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        {
          text: 'Agregar foto',
          className: 'btn-add',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/".$this->interfaz."/editElementFoto?servicio=".$data->id)?>','', '60');
          }
        }
      ],
      "ajax": {
        "url": "<?=$url("ecom/".$this->interfaz."/getAllFotos?id=".$data->id)?>",
        "dataSrc" : ""
      },
      "columns": [
        {"data": "id"},
        {"data": "tipo_descripcion"},
        {"data": "foto"},
        {
          "data": function(row){
            var sHtml = "";
            sHtml += "&nbsp;<button class=\"act-btn boton-editar\"><i class=\"fa fa-pencil\"></i></button>";
            if(row.estatus == '1'){
              sHtml += "&nbsp;<button class=\"act-btn boton-recoleccion\">Recolección</button>";
            }
            return sHtml;
          }
        }
      ],
      "language": {
        "url": "<?=$urlm("js/spanish.js");?>"
      },
      "order": [[ 0, "desc" ]]

    });

    $('#tabla-data tbody').on( 'click', 'button', function () {
      var data = tableData.row($(this).parents('tr')).data();
      var button = $(this);
      if(button.hasClass("boton-editar")){
        modalBootStrap('<?=$url("ecom/".$this->interfaz."/editElementFoto?servicio=".$data->id)?>&id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-recoleccion")){
        var url = "<?=$url("ecom/".$this->interfaz."/recoleccion")?>?id=" + data.id;
        window.location.href = url;
      }
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/".$this->interfaz."/destroy")?>", [], hDefault);
      }
    });

  });

  function finalizaRecoleccion(id){
    modificaModelYRegresa(id, "<?=$url("ecom/".$this->interfaz."/recolecta")?>", [], hDefault);
  }

  function modificaModelYRegresa(sModelId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var values = {
        id: sModelId
    };
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
        sUrl,
        values,
        function(data, textStatus, jqXHR) {
            console.log(data);
            if (data == undefined) {
                callback(false, objeto, "Error en la respuesta de la función invocada. No se regresó ninguna información.\n");
                return;
            }
            if (data.error != undefined) { objeto = data.error; }
            if (data.error) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
            callback(true, objeto, "Se actualizó la información");
            window.history.back();
        }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
        // Se invoca cuando la petición Ajax no pudo completarse
        // o cuando no se pudo interpretar adecuadamente la respuesta
        callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
        console.log(jqXHR);
    });
  }

</script>