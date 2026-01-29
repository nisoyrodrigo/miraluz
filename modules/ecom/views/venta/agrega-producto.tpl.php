<?php

$aUrl = $url("ecom/producto/getAllProductos");
if($tipo == "mica"){
  $aUrl = $url("ecom/producto/getAllMicas");
}
if($tipo == "extra"){
  $aUrl = $url("ecom/producto/getAllExtras");
}
?>




  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Seleccionar <?=$tipo?></h4>
  </div>

  <div class="modal-body">

    <div class="responsive-table">
      <table class="table" id="tabla-data-productos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Precio Público</th>
            <th></th>
          </tr>
        </thead>
      </table>
    </div>      
      
  </div>


 <script type="text/javascript">
  $(document).ready(function(){
    var tableDataProductos = $('#tabla-data-productos').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [],
      "ajax": {
          "url": "<?=$aUrl?>",
          "dataSrc": ""
      },
      "columns": [
          {"data": "id"},
          {"data": "nombre"},
          {"data": "precio"},
          {"data": "precio_publico"},
          {
              "data": function(row){
                  return `<button class="act-btn boton-seleccionar">Seleccionar</button>`;
              }
          }
      ],
      "language": {
          "url": "<?=$urlm("js/spanish.js");?>"
      },
      "order": [[3, "asc"]]
    });


    $('#tabla-data-productos tbody').on( 'click', 'button', function () {
      var data = tableDataProductos.row($(this).parents('tr')).data();
      var button = $(this);
      if(button.hasClass("boton-seleccionar")){
        console.log(data);
        modificaModel(data.id, "<?=$url("ecom/".$this->interfaz."/agregaProducto")?>", [], hDefault);
        $('#tabla-data').DataTable().ajax.reload();
        hideModal();
      }
    });

  });
 </script>