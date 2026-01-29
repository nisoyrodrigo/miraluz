<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Cotizaciones</h2>
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
                  <th>Cliente</th>
                  <th>Teléfono</th>
                  <th>Productos</th>
                  <th>Subtotal</th>
                  <th>Descuento</th>
                  <th>Total</th>
                  <th>Anticipo</th>
                  <th>Saldo</th>
                  <th>Vendedor</th>
                  <th>Fecha</th>
                  <th></th>
                  <th></th>
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
        "url": "<?=$url("ecom/".$this->interfaz."/getAllCotizaciones")?>",
        "dataSrc" : ""
      },
      "columns": [
        {"data": "id"},
        {"data": "nombre_cliente"},
        {"data": "telefono_cliente"},
        {"data": "productos_descripcion"},
        {"data": "subtotal"},
        {"data": "descuento"},
        {"data": "total"},
        {"data": "anticipo"},
        {"data": "saldo"},
        {"data": "vendedor_nombre"},
        {"data": "created"},
        {
          "data": function(row){
            var sHtml = "";
            if(row.permite_venta == "1"){
              sHtml += "&nbsp;<button class=\"act-btn boton-editar\"><i class=\"fa fa-pencil\"></i></button>";
            }
            return sHtml;
          }
        },
        {
          "data": function(row){
            var sHtml = "";
            sHtml += "&nbsp;<button class=\"act-btn boton-imprimir\"><i class=\"fas fa-print\"></i></button>";
            return sHtml;
          }
        },
        {
          "data": function(row){
            var sHtml = "";
            sHtml += "&nbsp;<button class=\"act-btn boton-archivo\"><i class=\"fas fa-file-pdf\"></i></button>";
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
        var url = "<?=$url("ecom/venta/editarCotizacion")?>?id=" + data.id;
        window.open(url, '_blank');
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
      if(button.hasClass("boton-recoleccion")){
        var url = "<?=$url("ecom/".$this->interfaz."/recoleccion")?>?id=" + data.id;
        window.location.href = url;
      }
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/".$this->interfaz."/destroy")?>", [], hDefault);
      }
      if(button.hasClass("boton-entrega")){
        modalBootStrapProducto('<?=$url("ecom/".$this->interfaz."/editEntrega")?>?id=' + data.id,'', '60');
      }
      if(button.hasClass("boton-archivo")){
        var url = "<?=$url("ecom/venta/imprimeticket")?>?id=" + data.id;
        window.open(url, '_blank');
      }
    });

  });
</script>