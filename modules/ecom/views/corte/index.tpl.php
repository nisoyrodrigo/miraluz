<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Corte</h2>
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
                  <th>Sucursal</th>
                  <th>Fecha</th>
                  <th>Usuario</th>
                  
                  <th>Total ingresos</th>
                  <th>Efectivo ing.</th>
                  <th>T. Débito</th>
                  <th>T. Crédito</th>
                  <th>Vales</th>
                  <th>Efectivo contado</th>
                  <th>Fondo</th>
                  <th>Depósito</th>

                  <th>Acciones</th>

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

  function money(v){
    var n = parseFloat(v);
    if(isNaN(n)) n = 0;
    return '$' + n.toFixed(2);
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
        {"data": "nombre_sucursal"},
        {"data": "fecha"},
        {"data": "nombre_usuario"},        
        // --- SNAPSHOT (si existen en ec_corte) ---
        {
          "data": function(row){
            // total ingresos del corte (suma de ingresos por forma)
            var total = 0;
            total += parseFloat(row.efectivo_ingreso || 0);
            total += parseFloat(row.tarjeta_ingreso || 0);
            total += parseFloat(row.tarjetac_ingreso || 0);
            total += parseFloat(row.vales_ingreso || 0);
            return money(total);
          },
          "title": "Total ingresos"
        },
        {
          "data": function(row){
            return money(row.efectivo_ingreso);
          },
          "title": "Efectivo ing."
        },
        {
          "data": function(row){
            return money(row.tarjeta_ingreso);
          },
          "title": "T. Débito"
        },
        {
          "data": function(row){
            return money(row.tarjetac_ingreso);
          },
          "title": "T. Crédito"
        },
        {
          "data": function(row){
            return money(row.vales_ingreso);
          },
          "title": "Vales"
        },
        {
          "data": function(row){
            return money(row.efectivo_contado);
          },
          "title": "Efectivo contado"
        },
        {
          "data": function(row){
            // fondo_caja si existe
            return money(row.fondo_caja);
          },
          "title": "Fondo"
        },
        {
          "data": function(row){
            // deposito sugerido guardado en el corte (snapshot)
            return money(row.deposito);
          },
          "title": "Depósito"
        },

        // --- ACCIONES ---
        {
          "data": function(row){
            var sHtml = "";

            // Detalle (modal)
            sHtml += "<button class=\"act-btn boton-info\" title=\"Detalle\"><i class=\"fa fa-info\"></i></button>";

            // Imprimir (nuevo)
            sHtml += "&nbsp;<a class=\"act-btn boton-print\" title=\"Imprimir\" target=\"_blank\" href=\"<?=$url("ecom/".$this->interfaz."/imprimeCorte")?>?id=" + row.id + "\">"
                   + "<i class=\"fa fa-print\"></i></a>";

            return sHtml;
          },
          "orderable": false,
          "searchable": false
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
      if(button.hasClass("boton-info")){
        modalBootStrap('<?=$url("ecom/".$this->interfaz."/detalleCorte")?>?id=' + data.id,'', '60');
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
    });

  });
</script>