<?php
$noImage = $urlm("images/image-holder.jpg");
$cliente = new Cliente($data->cliente);
$sucursal = new Sucursal($data->sucursal);
$vendedor = new Operador("WHERE user = ".$data->user);

$abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id)[0]->total ?? 0;
$rowsAbonos = VentaMovimiento::model()->findAll("WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$data->id);
$estatus = new VentaEstatus($data->estatus);
?>


<form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/".$this->interfaz."/saveAbono")?>" enctype="multipart/form-data">

  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Actualizar pago</h4>
  </div>

  <div class="modal-body">
      
    <input type="hidden" name="id" value="<?=$data->id?>" />

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Abonos</label>
      <div class="col-lg-10">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Monto</th>
              <th>Acción</th>
              <?if($this->user->id == "5" || $this->user->id == "1"){?>
                <th></th>
              <?}?>
            </tr>
          </thead>
          <tbody>
            <?php 
            $lastIndex = count($rowsAbonos) - 1; 
            foreach ($rowsAbonos as $i => $abono): ?>
              <tr>
                <td><?=date('d/m/Y', strtotime($abono->created))?></td>
                <td>$<?=number_format($abono->monto, 2)?></td>
                <td>
                  <button type="button" class="btn btn-primary btn-sm btn-print-abono" data-id="<?=$abono->id?>">
                    <i class="fa fa-print"></i> Imprimir
                  </button>
                </td>
                <?if($this->user->id == "5" || $this->user->id == "1" && $i == $lastIndex){?>
                  <td>
                    <button type="button" class="btn btn-primary btn-sm btn-delete-abono" data-id="<?=$abono->id?>">
                      <i class="fa fa-trash"></i> Eliminar
                    </button>
                  </td>
                <?}?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Nota</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->folio?>">
      </div>
    </div>

    <?if($data->estatus == "6"){?>
    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Estatus</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$estatus->nombre?>">
      </div>
    </div>
    <?}?>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Fecha</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->created?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Cliente</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$cliente->nombre." ".$cliente->apaterno." ".$cliente->amaterno;?>">
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Vendedor</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$vendedor->nombre." ".$vendedor->apaterno." ".$vendedor->amaterno;?>">
      </div>
    </div>



    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Total</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->total?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Abonado</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->anticipo + $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Saldo</label>
      <div class="col-lg-10">
        <input type="text" class="form-control" readonly value="<?=$data->saldo - $abonos?>">
      </div>
    </div>


    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Abonar pendiente</label>
      <div class="col-lg-10">
        <input type="number" max="<?=$data->saldo - $abonos?>" class="form-control" name="abono" value="<?=$data->saldo - $abonos?>" required>
      </div>
    </div>

    <div class="form-group row">
      <label class="col-lg-2 col-form-label">Forma de Pago</label>
      <div class="col-lg-10">
        <select class="form-control" id="forma_pago" name="forma_pago">
          <option value="efectivo">Efectivo</option>
          <option value="tarjeta">Tarjeta débito</option>
          <option value="tarjetac">Tarjeta crédito</option>
          <option value="vales">Vales</option>
        </select>
      </div>
    </div>

    <div class="form-group row" id="grupo_banco" style="display:none;">
      <label class="col-lg-2 col-form-label">Banco</label>
      <div class="col-lg-10">
        <select class="form-control" name="banco" id="banco">
          <option value="">Selecciona un banco</option>

          <option value="BBVA">BBVA</option>
          <option value="Banamex">Banamex</option>
          <option value="Santander">Santander</option>
          <option value="Banorte">Banorte</option>
          <option value="HSBC">HSBC</option>
          <option value="Scotiabank">Scotiabank</option>
          <option value="Inbursa">Inbursa</option>
          <option value="BanBajio">BanBajío</option>
          <option value="BancoAzteca">Banco Azteca</option>
          <option value="Afirme">Afirme</option>
          <option value="Banregio">Banregio</option>
          <option value="BanCoppel">BanCoppel</option>
          <option value="Multiva">Multiva</option>
          <option value="VePorMas">Ve por Más</option>
          <option value="Mifel">Mifel</option>

          <!-- Fintech -->
          <option value="NU">Nu</option>
          <option value="HeyBanco">Hey Banco</option>
          <option value="Klar">Klar</option>
          <option value="Uala">Ualá</option>
          <option value="Fondeadora">Fondeadora</option>

          <!-- Sistemas -->
          <option value="STP">STP</option>
          <option value="MercadoPago">Mercado Pago</option>
          <option value="Paypal">PayPal</option>
        </select>
      </div>
    </div>

    <div class="form-group row" id="grupo_digitos" style="display:none;">
      <label class="col-lg-2 col-form-label">Últimos 4 dígitos</label>
      <div class="col-lg-10">
        <input 
          type="text" 
          class="form-control" 
          name="tarjeta_digitos" 
          id="tarjeta_digitos" 
          maxlength="4"
          inputmode="numeric"
        >
      </div>
    </div>

      
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-white" data-dismiss="modal">Cerrar</button>
    <?if($data->estatus != "6"){?>
    <button id="btn-guardar" type="submit" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Guardando">Guardar</button>
    <?}?>
  </div>

 </form>

<script type="text/javascript">

  let submitConfirmado = false;
  $(document).ready(function(){

    $('.btn-print-abono').on('click', function () {
      const abonoId = $(this).data('id'); // Obtiene el ID del abono
      const pdfUrl = '<?=$url("ecom/venta/imprimeTicketAbono")?>' + '?id=' + abonoId;

      // Crear un iframe oculto y cargar el PDF
      const iframe = document.createElement('iframe');
      iframe.style.display = 'none'; // Ocultarlo
      iframe.src = pdfUrl;
      document.body.appendChild(iframe);

      // Esperar a que el PDF cargue y luego enviarlo a imprimir
      iframe.onload = function () {
        iframe.contentWindow.print();
      };
    });

    // REEMPLAZA este handler por el siguiente:
    $('.btn-delete-abono').off('click').on('click', function () {
      const abonoId = $(this).data('id');
      const $row = $(this).closest('tr');

      Swal.fire({
        title: '¿Eliminar abono?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true,
        focusCancel: true
      }).then((result) => {
        if (!result.isConfirmed) return;

        // Opcional: muestra cargando mientras elimina
        Swal.fire({
          title: 'Eliminando...',
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });

        // Llama a tu endpoint para eliminar
        $.ajax({
          url: '<?=$url("ecom/".$this->interfaz."/eliminaAbono")?>',
          type: 'POST',
          dataType: 'json',
          data: { id: abonoId },
          success: function (response) {
            if (response && response.error === undefined) {
              Swal.fire('Eliminado', 'El abono fue eliminado correctamente.', 'success');

              // Si tienes DataTable global, recárgalo:
              if ($.fn.DataTable.isDataTable('#tabla-data')) {
                $('#tabla-data').DataTable().ajax.reload(null, false);
              }

              // También puedes quitar la fila de la tabla de abonos del modal:
              $row.remove();

              // Si quieres cerrar el modal como antes:
              // hideModal();
            } else {
              Swal.fire('Error', (response && response.error) ? response.error : 'No se pudo eliminar el abono.', 'error');
            }
          },
          error: function () {
            Swal.fire('Error', 'Ocurrió un error al eliminar el abono.', 'error');
          }
        });
      });
    });


    $('.select2').select2({
      dropdownParent: $('#myModal')
    });


    $("#formEdit").on('submit', function(e){

      if (submitConfirmado) {
        // dejar pasar → ajaxForm
        return true;
      }

      e.preventDefault();

      $("#btn-guardar")
        .prop('disabled', true)
        .html("<i class='fa fa-spinner fa-spin'></i> Guardando");

      const saldo = parseFloat("<?=$data->saldo - $abonos?>");
      const abono = parseFloat($('input[name="abono"]').val() || 0);

      const forma = $('#forma_pago').val();

      if (forma === 'tarjeta' || forma === 'tarjetac') {
        const banco = $('#banco').val();
        const digitos = $('#tarjeta_digitos').val();

        if (!banco) {
          alertMessage("Debes seleccionar el banco.");
          resetBtn();
          return;
        }

        if (!/^\d{4}$/.test(digitos)) {
          alertMessage("Debes ingresar exactamente los últimos 4 dígitos de la tarjeta.");
          resetBtn();
          return;
        }
      }

      // SI LIQUIDA
      if (abono >= saldo) {
        Swal.fire({
          title: 'Nota liquidada',
          text: '¿Se entrega el producto en este momento?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, entregar',
          cancelButtonText: 'No, solo registrar pago',
          reverseButtons: true
        }).then((result) => {

          if ($('#entregar').length === 0) {
            $('<input>', {
              type: 'hidden',
              name: 'entregar',
              id: 'entregar'
            }).appendTo('#formEdit');
          }

          $('#entregar').val(result.isConfirmed ? 1 : 0);

          submitConfirmado = true;
          $("#formEdit").trigger('submit'); // deja pasar
        });

        return;
      }

      // ABONO NORMAL
      submitConfirmado = true;
      $("#formEdit").trigger('submit');
    });


    $('#formEdit').ajaxForm(function(response) { 
      console.log(response);

      submitConfirmado = false;
      resetBtn();
      if(response.error == undefined){
        alertMessage("Información guardada satisfactoriamente.", "success");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();

        if (response.id) {
          const pdfUrl = '<?=$url("ecom/venta/imprimeTicketAbono")?>' + '?id=' + response.id;
          const iframe = document.createElement('iframe');
          iframe.style.display = 'none'; // Ocultarlo
          iframe.src = pdfUrl;
          document.body.appendChild(iframe);
          // Esperar a que el PDF cargue y luego enviarlo a imprimir
          iframe.onload = function () {
            iframe.contentWindow.print();
          };
        }

      } else {
        alertMessage(response.error);
        console.log("Error al guardar información");
      }
    });

    function resetBtn(){
      $("#btn-guardar").prop('disabled', false).html("Guardar");
    }

    $('#forma_pago').on('change', function () {
      const forma = $(this).val();

      if (forma === 'tarjeta' || forma === 'tarjetac') {
        $('#grupo_banco').show();
        $('#grupo_digitos').show();
      } else {
        $('#grupo_banco').hide();
        $('#banco').val('');

        $('#grupo_digitos').hide();
        $('#tarjeta_digitos').val('');
      }
    });

    // Solo números, máx 4
    $('#tarjeta_digitos').on('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 4);
    });




  });


  /*––––––––––––––––––––––– /Producto fotos ––––––––––––––––––––––-––––––*/
</script>