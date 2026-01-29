<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Servicios</h2>
    <ol class="breadcrumb">
      
    </ol>
  </div>
  <div class="col-lg-2">

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
                  <th>Serie</th>
                  <th>Operador</th>
                  <th>Origen</th>
                  <th>Destino</th>
                  <th>Estatus</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">

  if (window.opener) {
      window.opener.location.reload();
  }
  
  $(document).ready(function() {

    var tableData = $('#tabla-data').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        {
          text: 'Agregar',
          className: 'btn-add',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/".$this->interfaz."/editElement")?>','', '60');
          }
        }
      ],
      "ajax": {
        "url": "<?=$url("ecom/".$this->interfaz."/getAll")?>",
        "dataSrc" : ""
      },
      "columns": [
        {"data": "id"},
        {"data": "no_serie"},
        {"data": "operador_nombre"},
        {"data": "origen"},
        {"data": "destino"},
        {"data": "estatus_descripcion"},
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
        modalBootStrap('<?=$url("ecom/".$this->interfaz."/editElement")?>?id=' + data.id,'', '60');
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
</script>