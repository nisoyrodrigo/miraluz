<?
$optometristas = Operador::model()->findAll("WHERE optometrista = 1");
$operador = new Operador("WHERE user = ".$this->user->id);
$sucursales = Sucursal::model()->findAll("WHERE estatus = 1 AND id IN (".$operador->sucursales.")");

$graduaciones = [
  'esfera' => range(-20.00, 9.00, 0.25), // Genera valores de -20.00 a 9.00 en incrementos de 0.25
  'cilindro' => range(-8.00, 0.00, 0.25), // Genera valores de -8.00 a 8.00 en incrementos de 0.25
  'eje' => range(0, 180, 5), // Genera valores de 0 a 180 en incrementos de 5
  'add' => range(0.75, 3.50, 0.25) // Genera valores de +0.75 a +3.50 en incrementos de 0.25
];

?>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Punto de venta</h2>
    <ol class="breadcrumb">
      
    </ol>
  </div>
  <div class="col-lg-2">

  </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row cotizacion-bg">

    <div class="col-lg-12">


        <input type="hidden" id="ventaClienteId" value="1">

        <div class="form-group row">
          <div class="col-lg-10">
            <input type="text" class="form-control" id="nombreCliente" readonly value="CLIENTE MOSTRADOR">
          </div>
          <div class="col-lg-2">
            <button type="button" class="btn btn-primary" onclick="eligeCliente()">Buscar</button>
          </div>
        </div>

        <hr>

        <!-- Grupo de selección de tipo de visión -->
        <h5>Tipo de Visión</h5>
        <div class="row">
          <div class="col-3">
            <label><input type="radio" name="tipo_vision" value="vision_sencilla" checked onchange="actualizarGraduacion()"> Visión sencilla</label>
          </div>
          <div class="col-3">
            <label><input type="radio" name="tipo_vision" value="flap_top" onchange="actualizarGraduacion()"> Flap top</label>
          </div>
          <div class="col-3">
            <label><input type="radio" name="tipo_vision" value="blend" onchange="actualizarGraduacion()"> Blend</label>
          </div>
          <div class="col-3">
            <label><input type="radio" name="tipo_vision" value="progresivo" onchange="actualizarGraduacion()"> Progresivo</label>
          </div>
        </div>

        <hr>

        <h5>Graduación</h5>
        <div class="row">
          <div class="col-9">
            <div class="responsive-table">
              <table class="table table-bordered table-graduacion">
                <tr>
                  <th></th>
                  <th>Esfera</th>
                  <th>Cilindro</th>
                  <th>Eje</th>
                  <th class="col-adicion">ADD</th>
                  <th>DNP</th>
                  <th class="col-altura">Altura</th>
                </tr>
                <tr>
                  <td>OD</td>
                  <td>
                    <select name="esfera_od" class="form-control select2">
                    <?php foreach ($graduaciones['esfera'] as $value): 
                      $formattedValue = $value > 0 ? '+' . number_format($value, 2) : number_format($value, 2);
                      ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= $formattedValue ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <select name="cilindro_od" class="form-control select2">
                    <?php foreach ($graduaciones['cilindro'] as $value): ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 2) ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <select name="eje_od" class="form-control select2">
                    <?php foreach ($graduaciones['eje'] as $value): ?>
                      <option value="<?= number_format($value, 0) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 0) ?>°</option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td class="col-adicion">
                    <select name="add_od" class="form-control select2">
                    <?php foreach ($graduaciones['add'] as $value): ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 2) ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="text" class="form-control" name="dnp_od" value="<?=$data->dnp_od?>"></td>
                  <td class="col-altura"><input type="text" class="form-control" name="altura_od" value="<?=$data->altura_od?>"></td>
                </tr>
                <tr>
                  <td>OI</td>
                  <td>
                    <select name="esfera_oi" class="form-control select2">
                    <?php foreach ($graduaciones['esfera'] as $value): 
                      $formattedValue = $value > 0 ? '+' . number_format($value, 2) : number_format($value, 2);
                      ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= $formattedValue ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <select name="cilindro_oi" class="form-control select2">
                    <?php foreach ($graduaciones['cilindro'] as $value): ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 2) ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td>
                    <select name="eje_oi" class="form-control select2">
                    <?php foreach ($graduaciones['eje'] as $value): ?>
                      <option value="<?= number_format($value, 0) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 0) ?>°</option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td class="col-adicion">
                    <select name="add_oi" class="form-control select2">
                    <?php foreach ($graduaciones['add'] as $value): ?>
                      <option value="<?= number_format($value, 2) ?>" <?= $value == 0 ? 'selected' : '' ?>><?= number_format($value, 2) ?></option>
                    <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="text" class="form-control" name="dnp_oi" value="<?=$data->dnp_oi?>"></td>
                  <td class="col-altura"><input type="text" class="form-control" name="altura_oi" value="<?=$data->altura_oi?>"></td>
                </tr>
              </table>
            </div>

            <div class="row mb-4">
              <div class="col-3">
                <button type="button" class="btn btn-primary" onclick="modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?tipo=armazon")?>','', '60');">Agregar producto</button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-primary" onclick="modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?tipo=mica")?>','', '60');">Agregar mica</button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-primary" onclick="modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?tipo=extra")?>','', '60');">Agregar tratamiento</button>
              </div>
              <div class="col-3">
                <button type="button" class="btn btn-primary">Borrar</button>
              </div>
            </div>

            <div class="responsive-table">
              <table class="table" id="tabla-data">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                  </tr>
                </thead>
              </table>
            </div>

            <div class="row mt-4 mb-4">
              <label class="col-form-label">Comentarios</label>
              <textarea class="form-control" id="comentarios" rows="3"></textarea>
            </div>


          </div>


          <div class="col-3">

            <div class="form-group">
              <label>Sucursal</label>
              <select class="form-control" id="sucursal" name="sucursal">
                <?foreach ($sucursales as $key => $value) {?>
                <option value="<?=$value->id?>"><?=$value->nombre?></option>
                <?}?>
              </select>
            </div>
            <div class="form-group">
              <label>Optometrista</label>
              <select class="form-control" id="optometrista" name="optometrista">
                <option value="">Selecciona una opción...</option>
                <?foreach ($optometristas as $key => $value) {?>
                <option value="<?=$value->id?>"><?=$value->nombre?> <?=$value->apaterno?> <?=$value->amaterno?></option>
                <?}?>
              </select>
            </div>
            <div class="form-group">
              <label>Subtotal</label>
              <input type="text" class="form-control" id="subtotal" value="" readonly>
            </div>
            <div class="form-group">
              <label>Descuento</label>
              <input type="number" class="form-control" id="descuento" value="0">
            </div>
            <div class="form-group">
              <label>Total</label>
              <input type="text" class="form-control" id="total" value="0" readonly>
            </div>
            <div class="form-group">
              <label>Anticipo</label>
              <input type="number" class="form-control" id="anticipo" value="0">
            </div>
            <div class="form-group">
              <label>Forma de Pago</label>
              <select class="form-control" id="forma_pago">
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta débito</option>
                <option value="tarjetac">Tarjeta crédito</option>
                <option value="vales">Vales</option>
              </select>
            </div>
            <div class="form-group">
              <label>Saldo</label>
              <input type="text" class="form-control" id="saldo" readonly>
            </div>
            
            <div class="form-group row">
              <div class="col-6 text-start">
                <button type="button" class="btn btn-secondary w-100" onclick="guardarVenta('cotizacion')">Cotizar</button>
              </div>
              <div class="col-6 text-end">
                <button type="button" class="btn btn-primary w-100" onclick="guardarVenta('venta')">Venta</button>
              </div>
            </div>

          </div>


        </div>

    </div>

  </div>
</div>

<script type="text/javascript">

  // Función para mostrar/ocultar las columnas de "ADD" y "Altura" según el tipo de visión seleccionado
  function actualizarGraduacion() {
    var tipoVision = document.querySelector('input[name="tipo_vision"]:checked').value;

    if (tipoVision === 'vision_sencilla') {
      document.querySelectorAll('.col-adicion, .col-altura').forEach(function(col) {
        col.style.display = 'none';
      });
    } else {
      document.querySelectorAll('.col-adicion, .col-altura').forEach(function(col) {
        col.style.display = '';
      });
    }
  }

  // Llama a la función al cargar la página para asegurarse de que las columnas estén correctamente ocultas o mostradas
  document.addEventListener('DOMContentLoaded', actualizarGraduacion);

  if (window.opener) {
      window.opener.location.reload();
  }
  
  $(document).ready(function() {
    $('.select2').select2({
      width: '100%'
    });
    $('body').addClass('mini-navbar');

    var tableData = $('#tabla-data').DataTable({
      pageLength: 10,
      responsive: true,
      dom: 't', // Solo muestra la tabla, sin botones, encabezados ni barra de búsqueda
      paging: false, // Desactiva la paginación
      info: false, // Oculta el pie de página con información de filas
      searching: false, // Desactiva la barra de búsqueda

      "ajax": {
          "url": "<?=$url("ecom/".$this->interfaz."/getAllTemporales")?>",
          "dataSrc": ""
      },
      "columns": [
          {"data": "id"},
          {"data": "nombre"},
          {"data": "familia"},
          {"data": "cantidad"},
          {"data": "precio_publico"},
          {"data": "total"},
          {
              "data": function(row){
                  return `<button class=\"act-btn boton-eliminar\"><i class=\"fa fa-trash\"></i></button>`;
              }
          }
      ],
      "language": {
          "url": "<?=$urlm("js/spanish.js");?>"
      },
      "order": [[0, "desc"]],
      "drawCallback": calculateTotals
    });

    $('#tabla-data tbody').on( 'click', 'button', function () {
      var data = tableData.row($(this).parents('tr')).data();
      var button = $(this);
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/".$this->interfaz."/destroyTemporal")?>", [], hDefault);
      }
    });

    $('#descuento, #anticipo').on('input', calculateTotals);

    function calculateTotals() {
      let subtotal = 0;

      tableData.rows().every(function() {
        let data = this.data();
        subtotal += parseFloat(data.total || 0);
      });

      // Obtener valores de descuento y anticipo, considerando que pueden ser 0 o vacíos
      let descuento = parseFloat($('#descuento').val()) || 0;
      let anticipo = parseFloat($('#anticipo').val()) || 0;

      // Calcular total y saldo
      let total = subtotal - descuento;
      let saldo = total - anticipo;

      // Actualizar campos en la interfaz
      $('#subtotal').val(subtotal.toFixed(2));
      $('#total').val(total.toFixed(2));
      $('#saldo').val(saldo.toFixed(2));
    }

    $('#tabla-data').DataTable().ajax.reload(calculateTotals);


  });

  function eligeCliente(){
    modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregaCliente")?>','', '60');
  }

  function guardarVenta(tipo) {
    const data = {
      cliente: $('#ventaClienteId').val(),
      comentarios: $('#comentarios').val(),
      tipo_vision: $('input[name="tipo_vision"]:checked').val(),

      od_esfera: $('select[name="esfera_od"]').val(),
      od_cilindro: $('select[name="cilindro_od"]').val(),
      od_eje: $('select[name="eje_od"]').val(),
      od_add: $('select[name="add_od"]').val(),
      od_dnp: $('input[name="dnp_od"]').val(),
      od_altura: $('input[name="altura_od"]').val(),
      oi_esfera: $('select[name="esfera_oi"]').val(),
      oi_cilindro: $('select[name="cilindro_oi"]').val(),
      oi_eje: $('select[name="eje_oi"]').val(),
      oi_add: $('select[name="add_oi"]').val(),
      oi_dnp: $('input[name="dnp_oi"]').val(),
      oi_altura: $('input[name="altura_oi"]').val(),

      optometrista: $('#optometrista').val(),
      sucursal: $('#sucursal').val(),
      subtotal: parseFloat($('#subtotal').val()) || 0,
      descuento: parseFloat($('#descuento').val()) || 0,
      total: parseFloat($('#total').val()) || 0,
      anticipo: parseFloat($('#anticipo').val()) || 0,
      forma_pago: $('#forma_pago').val(),
      saldo: parseFloat($('#saldo').val()) || 0,
      tipo: tipo // Tipo de operación: "cotizacion" o "venta"
    };

    var sError = "";

    if (data.cliente == "1") {
      enviarSolicitudVenta(data, tipo);
      return;
    }

    if(data.cliente == ""){
      sError += "Debes seleccionar un cliente.\n";
    }

    if (data.tipo_vision !== 'vision_sencilla') {
      // Validar campos de altura y adición si el tipo de visión no es "Visión sencilla"
      const camposConAlturaAdicion = [
        data.od_esfera, data.od_cilindro, data.od_eje, data.od_add, data.od_dnp, data.od_altura,
        data.oi_esfera, data.oi_cilindro, data.oi_eje, data.oi_add, data.oi_dnp, data.oi_altura
      ];
      const campoVacio = camposConAlturaAdicion.some(campo => campo === "");

      if (campoVacio) {
        sError += "Por favor, completa todos los campos de graduación, incluyendo altura y adición.\n";
      }
    } else {
      // Validar campos sin altura y adición para "Visión sencilla"
      const camposSinAlturaAdicion = [
        data.od_esfera, data.od_cilindro, data.od_eje, data.dnp_od,
        data.oi_esfera, data.oi_cilindro, data.oi_eje, data.dnp_oi
      ];
      const campoVacio = camposSinAlturaAdicion.some(campo => campo === "");

      if (campoVacio) {
        sError += "Por favor, completa todos los campos de graduación para visión sencilla.\n";
      }
    }

    if(sError != ""){
      alertMessage(sError);
      return; // Detiene la ejecución si hay campos vacíos
    }

    // Envía la solicitud si todas las validaciones se cumplen
    enviarSolicitudVenta(data, tipo);
  }

  function enviarSolicitudVenta(data, tipo) {
    var sucursal = $('#sucursal').val();

    modalBootStrapCallback(
      '<?=$url("ecom/".$this->interfaz."/confirmaVendedor")?>?sucursal=' + sucursal,
      function callback(vendedorData) {
        if (vendedorData) {
          data.vendedor_id = vendedorData.id; // Agrega el vendedor seleccionado al data
          data.vendedor_nombre = vendedorData.nombre;

          enviarSolicitudVentaS2(data, tipo); // Llama al siguiente paso con los datos actualizados
        }
      },
      '60'
    );
  }

  function enviarSolicitudVentaS2(data, tipo) {
    $.post('<?=$url("ecom/".$this->interfaz."/saveVenta")?>', data)
      .done(function(response) {
          console.log(response);
          if (response.error === undefined) {
              alertMessage("Información guardada satisfactoriamente.", "success");
              if (response.id) {
                const pdfUrl = '<?=$url("ecom/venta/imprimeticket")?>' + '?id=' + response.id;

                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = pdfUrl;
                document.body.appendChild(iframe);

                iframe.onload = function() {
                  iframe.contentWindow.print();
                };
              }

              limpiarFormulario();
          } else {
            alertMessage(response.error);
            console.log("Error al guardar información");
          }
      })
      .fail(function() {
        alertMessage("Ocurrió un error al guardar la información.");
      });
  }


  function limpiarFormulario() {
    $('#ventaClienteId').val('');
    $('#nombreCliente').val('Nombre cliente');
    $('#comentarios').val('');
    $('input[name="tipo_vision"][value="vision_sencilla"]').prop('checked', true);
    actualizarGraduacion();

    // Reiniciar los valores de los select2
    $('select[name="esfera_od"], select[name="cilindro_od"], select[name="eje_od"], select[name="add_od"]').val('').trigger('change');
    $('select[name="esfera_oi"], select[name="cilindro_oi"], select[name="eje_oi"], select[name="add_oi"]').val('').trigger('change');
    
    // Reiniciar los valores de los inputs de texto
    $('input[name="dnp_od"], input[name="altura_od"]').val('');
    $('input[name="dnp_oi"], input[name="altura_oi"]').val('');
  

    $('#optometrista').val('');
    $('#subtotal').val('');
    $('#descuento').val('0');
    $('#total').val('0');
    $('#anticipo').val('0');
    $('#forma_pago').val('efectivo');
    $('#saldo').val('');
    $('#tabla-data').DataTable().ajax.reload();
  }
</script>