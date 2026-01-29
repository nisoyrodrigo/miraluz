<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Roles</h2>
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
                  <th>Id</th>
                  <th>Nombre</th>
                  <th>Creado</th>
                  <th>Modificado</th>
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
  
  $(document).ready(function() {

    var tableData = $('#tabla-data').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [
        {
          text: 'Nuevo rol',
          className: 'btn-primary',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/rol/editElement")?>','', '60');
          }
        }
      ],
      "ajax": {
        "url": "<?=$url("ecom/rol/getAll")?>",
        "dataSrc" : ""
      },
      "columns": [
        {"data": "id"},
        {"data": "name"},
        {"data": "created"},
        {"data": "modified"},
        {
            "data": null,
            "targets": -1,
            "defaultContent":"<button class=\"boton-editar\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></button>&nbsp;<button class=\"boton-eliminar\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button>&nbsp;<button class=\"boton-permisos\"><i class=\"fa fa-lock\" aria-hidden=\"true\"></i></button>"
        }
      ],
      "language": {
        "url": "<?=$urlm("js/spanish.js");?>"
      }

    });

    $('#tabla-data tbody').on( 'click', 'button', function () {
      var data = tableData.row($(this).parents('tr')).data();
      var button = $(this);
      if(button.hasClass("boton-editar")){
        modalBootStrap('<?=$url("ecom/rol/editElement")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/rol/destroy")?>", [], hDefault);
      }
      if(button.hasClass("boton-permisos")){
        modalBootStrap('<?=$url("ecom/rol/setPermisos")?>?id=' + data.id,'', '50');
      }
    });

  });
</script>