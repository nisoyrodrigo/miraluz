<?

$operador = new Operador("WHERE user = ".$this->user->id);
$sucursales = Sucursal::model()->findAll("WHERE estatus = 1 AND id IN (".$operador->sucursales.")");
$listaEstatus = VentaEstatus::model()->findAll();


$sucursalesArray = explode(',', $operador->sucursales); // Divide "1,2" en un array [1, 2]
$condiciones = [];
foreach ($sucursalesArray as $sucursal) {
  $condiciones[] = "FIND_IN_SET('$sucursal', sucursales)";
}
$whereCondicionVendedores = implode(' OR ', $condiciones);

$vendedores = Operador::model()->findAll("
  WHERE user != 1 AND estatus = 1 AND ($whereCondicionVendedores)
");


$optometristas = Operador::model()->findAll("WHERE user != 1 AND estatus = 1 AND optometrista = 1 AND ($whereCondicionVendedores)");

?>

<style>
  /* Contenedor general del formulario */
  .form-inline {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }

  /* Inputs y selects más pequeños */
  .form-control-sm {
    font-size: 14px;
    padding: 5px;
  }

  /* Botones pequeños */
  .btn-sm {
    font-size: 14px;
    padding: 5px 10px;
  }

  /* Etiquetas de formulario alineadas */
  .form-group label {
    font-weight: bold;
    margin-bottom: 0;
  }

  /* Sombra y bordes redondeados para el formulario */
  .shadow-sm {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  }

  .rounded {
    border-radius: 5px;
  }
</style>

<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Reporte de ventas</h2>
    <ol class="breadcrumb">
      
    </ol>
  </div>
  <div class="col-lg-2">

  </div>
</div>

<div class="row mt-4">
  <div class="col-lg-12">
    <form id="formBusqueda" method="post" class="form-inline justify-content-center mb-3 p-3 shadow-sm bg-light rounded">
      <div class="form-group mx-2">
        <label for="bcliente" class="mr-2">Cliente</label>
        <input type="text" class="form-control form-control-sm" name="bcliente" placeholder="Nombre del cliente">
      </div>
      <div class="form-group mx-2">
        <label for="btelefono" class="mr-2">Teléfono</label>
        <input type="text" class="form-control form-control-sm" name="btelefono" placeholder="Teléfono">
      </div>
      <div class="form-group mx-2">
        <label for="bsucursal" class="mr-2">Sucursal</label>
        <select class="form-control form-control-sm" name="bsucursal">
          <option value="">Seleccionar...</option>
          <?foreach ($sucursales as $key => $value) {?>
            <option value="<?=$value->id?>"><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
      <div class="form-group mx-2">
        <label for="bvendedor" class="mr-2">Vendedor</label>
        <select class="form-control form-control-sm" name="bvendedor">
          <option value="">Seleccionar...</option>
          <?foreach ($vendedores as $key => $value) {?>
            <option value="<?=$value->user?>"><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
          <?}?>
        </select>
      </div>
      <div class="form-group mx-2">
        <label for="boptometrista" class="mr-2">Optometrista</label>
        <select class="form-control form-control-sm" name="boptometrista">
          <option value="">Seleccionar...</option>
          <?foreach ($optometristas as $key => $value) {?>
            <option value="<?=$value->id?>"><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
          <?}?>
        </select>
      </div>
      <div class="form-group mx-2">
        <label for="bestatus" class="mr-2">Estatus</label>
        <select class="form-control form-control-sm" name="bestatus">
          <option value="">Seleccionar...</option>
          <?foreach ($listaEstatus as $key => $value) {?>
            <option value="<?=$value->id?>"><?=$value->nombre?></option>
          <?}?>
        </select>
      </div>
      <div class="form-group mx-2">
        <label for="btelefono" class="mr-2">Inicio</label>
        <input type="date" class="form-control form-control-sm" name="bfecha_inicio" placeholder="Fecha inicio">
      </div>
      <div class="form-group mx-2">
        <label for="btelefono" class="mr-2">Fin</label>
        <input type="date" class="form-control form-control-sm" name="bfecha_fin" placeholder="Fecha fin">
      </div>
      <div class="form-group mx-2">
        <button type="button" class="btn btn-primary btn-sm mr-2" onclick="filtraContenido();">
          <i class="fas fa-search"></i> Buscar
        </button>
        <button type="button" class="btn btn-secondary btn-sm mr-2" onclick="borraForm();">
          <i class="fas fa-trash"></i> Limpiar
        </button>
        <button type="button" class="btn btn-danger btn-sm mr-2" onclick="generaReportePDF();">
          <i class="fas fa-file-pdf"></i> Imprimir PDF
        </button>
        <button type="button" class="btn btn-danger btn-sm" onclick="generaReporteCSV();">
          <i class="fas fa-file-pdf"></i> Generar Excel
        </button>
      </div>
    </form>
  </div>
</div>

<!--
<div class="row mt-4">
  <div class="col-lg-12">
    <div class="table-responsive white-bg">
      <table class="table">
        <tr>
          <th></th>
          <th></th>
          <th>Cliente</th>
          <th>Teléfono</th>
          <th>Sucursal</th>
          <th>Vendedor</th>
          <th>Optometrista</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th>Estatus</th>
        </tr>
        <tr>
          <td><a class="btn btn-primary btn-agrega" onclick="filtraContenido();"><i class="fas fa-search"></i></a></td>
          <td><a class="btn btn-primary btn-agrega" onclick="borraForm();"><i class="fas fa-trash"></i></a></td>
          <form id="formBusqueda" method="post">
            <td><input type="text" class="form-control" name="bcliente"></td>
            <td><input type="text" class="form-control" name="btelefono"></td>
            <td style="width: 200px;">
              <select class="form-control select2 w-100" name="bsucursal">
                <option value="">Sucursal...</option>
                <?foreach ($sucursales as $key => $value) {?>
                <option value="<?=$value->id?>"><?=$value->nombre?></option>
                <?}?>
              </select>
            </td>
            <td style="width: 200px;">
              <select class="form-control select2 w-100" name="bvendedor">
                <option value="">Vendedores...</option>
                <?foreach ($vendedores as $key => $value) {?>
                <option value="<?=$value->user?>"><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
                <?}?>
              </select>
            </td>
            <td style="width: 200px;">
              <select class="form-control select2 w-100" name="boptometrista">
                <option value="">Optometrista...</option>
                <?foreach ($optometristas as $key => $value) {?>
                <option value="<?=$value->id?>"><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
                <?}?>
              </select>
            </td>
            <td><input type="date" class="form-control" name="bfecha_inicio"></td>
            <td><input type="date" class="form-control" name="bfecha_fin"></td>
            <td style="width: 200px;">
              <select class="form-control select2 w-100" name="bestatus">
                <option value="">Estatus...</option>
                <?foreach ($listaEstatus as $key => $value) {?>
                <option value="<?=$value->id?>"><?=$value->nombre?></option>
                <?}?>
              </select>
            </td>
          </form>
        </tr>
      </table>
    </div>
  </div>
</div>
-->

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-content no-search">
          <div class="table-responsive">
            <table id="tabla-data" class="table table-striped table-hover data-table">
              <thead>
                <tr>
                  <th>Id</th>
                  <th>Sucursal</th>
                  <th>Vendedor</th>
                  <th>Optometrista</th>
                  <th>Cliente</th>
                  <th>Teléfono</th>
                  <th>Productos</th>
                  <th>Subtotal</th>
                  <th>Descuento</th>
                  <th>Total</th>
                  <th>Anticipo</th>
                  <th>Abonos</th>
                  <th>Saldo</th>
                  <th>Fecha</th>
                  <th>Estatus</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th colspan="7" style="text-align:right;">Totales:</th>
                  <th id="footer-subtotal">0.00</th>
                  <th id="footer-descuento">0.00</th>
                  <th id="footer-total">0.00</th>
                  <th id="footer-anticipo">0.00</th>
                  <th id="footer-abonos">0.00</th>
                  <th id="footer-saldo">0.00</th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var selectedIds = [];
  var isSelectAll = false;
  $(document).ready(function() {


    function getSelectedIds() {
      selectedIds;
    }


    var tableData = $('#tabla-data').DataTable({
      pageLength: 10,
      responsive: true,
      dom: '<"html5buttons"B>lTfgitp',
      buttons: [

      ],
      "pagingType": "full_numbers",
      "paging": true,
      "bProcessing": true,
      "serverSide": true,
      "ajax": {
        "url": "<?=$url("ecom/".$this->interfaz."/getReporteVentas")?>",
        "type" : "post"
      },
      "columns": [
        {"data": "id"},
        {"data": "sucursal_nombre"},
        {"data": "vendedor_nombre"},
        {"data": "optometrista_nombre"},
        {"data": "cliente_nombre"},
        {"data": "cliente_telefono"},
        {"data": "productos_descripcion"},
        {"data": "subtotal"},
        {"data": "descuento"},
        {"data": "total"},
        {"data": "anticipo"},
        {"data": "abonos"},
        {"data": "saldo"},
        {"data": "fecha_venta"},
        {"data": "estatus_descripcion"}
      ],
      "language": {
        "url": "<?=$urlm("js/spanish.js");?>"
      },
      "order": [ 0, 'desc' ],
      footerCallback: function (row, data, start, end, display) {
        var json = this.api().ajax.json();

        $('#footer-subtotal').html(json.totals.subtotal);
        $('#footer-descuento').html(json.totals.descuento);
        $('#footer-total').html(json.totals.total);
        $('#footer-anticipo').html(json.totals.anticipo);
        $('#footer-abonos').html(json.totals.abonos);
        $('#footer-saldo').html(json.totals.saldo);
      }
    });

    $('#tabla-data tbody').on( 'click', 'button', function () {
      var data = tableData.row($(this).parents('tr')).data();
      var button = $(this);
     
    });

  });


  function filtraContenido(){
    console.log('filtraContenido');
    var data = $('#formBusqueda').serialize();
    console.log(data);
    $('#tabla-data').DataTable().ajax.url("<?=$url("ecom/".$this->interfaz."/getReporteVentas")?>?" + data).load();
  }

  function borraForm(){
    $('#formBusqueda').trigger("reset");
    filtraContenido();
  }

  function generaReportePDF() {
    var data = $('#formBusqueda').serialize();
    window.open("<?=$url('ecom/'.$this->interfaz.'/generarReportePDF')?>?" + data, "_blank");
  }

  function generaReporteCSV() {
    var data = $('#formBusqueda').serialize();
    window.open("<?=$url('ecom/'.$this->interfaz.'/generarReporteCSV')?>?" + data, "_blank");
  }

</script>