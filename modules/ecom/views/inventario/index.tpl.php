<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Inventario</h2>
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
                  <th>Producto</th>
                  <th>Familia</th>
                  <th>Principal</th>
                  <th>Macroplaza</th>
                  <th>Ecatepec</th>
                  <th>Flores</th>
                  <th>Tizayuca</th>
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
          text: 'Exportar a Excel',
          action: function ( e, dt, button, config ) {
              // Redirige a la URL de exportación de CSV
              window.location.href = "<?=$url('ecom/'.$this->interfaz.'/exportaCSV')?>";
          }
        },
        {
          text: 'Exportar a PDF',
          action: function ( e, dt, button, config ) {
              // Redirige a la URL de exportación de CSV
              window.location.href = "<?=$url('ecom/'.$this->interfaz.'/generarpdfinventario')?>";
          }
        },
      ],
      "pagingType": "full_numbers",
      "paging": true,
      "bProcessing": true,
      "serverSide": true,
      "ajax": {
        "url": "<?=$url("ecom/".$this->interfaz."/getAll")?>",
        "type" : "post"
      },
      "columns": [
        {"data": "id"},
        {"data": "nombre"},
        {"data": "familia"},
        {"data": "almacen5"},
        {"data": "almacen1"},
        {"data": "almacen3"},
        {"data": "almacen2"},
        {"data": "almacen4"},
        {
          "data": function(row){
            var sHtml = "";
            //sHtml += "&nbsp;<button class=\"act-btn boton-editar\"><i class=\"fas fa-pencil\"></i></button>";
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
        var url = "<?=$url("ecom/".$this->interfaz."/editar")?>?id=" + data.id;
        window.location.href = url;
      }
      if(button.hasClass("boton-imprimir")){

        const pdfUrl = '<?=$url("ecom/venta/imprimeticket")?>' + '?id=' + data.id;
            
        // Crear un iframe oculto y cargar el PDF
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none'; // Ocultarlo
        iframe.src = pdfUrl;
        document.body.appendChild(iframe);

        // Esperar un momento para que cargue y luego enviarlo a imprimir
        iframe.onload = function() {
          iframe.contentWindow.print();
        };
      }
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/".$this->interfaz."/destroy")?>", [], hDefault);
      }
      if(button.hasClass("boton-entrega")){
        modalBootStrapProducto('<?=$url("ecom/".$this->interfaz."/editEntrega")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-detalle")){
        modalBootStrapProducto('<?=$url("ecom/".$this->interfaz."/detalle")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-abono")){
        modalBootStrapProducto('<?=$url("ecom/".$this->interfaz."/editAbono")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-estatus")){
        modalBootStrapProducto('<?=$url("ecom/".$this->interfaz."/editEstatus")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-archivo")){
        var url = "<?=$url("ecom/venta/imprimeticket")?>?id=" + data.id;
        window.open(url, '_blank');
      }
    });

  });
</script>