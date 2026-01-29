<?
$almacenOrigen = new Almacen($data->almacen_origen);
$almacenDestino = new Almacen($data->almacen_destino);
$operador = new Operador("WHERE user = ".$data->user_envia);
$operadorRecibe = new Operador("WHERE user = ".$data->user_recibe);
?>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
    <h2>Editar transferencia</h2>
    <ol class="breadcrumb">
      
    </ol>
    <div class="row px-0">
      <div class="table-responsive">
        <table class="table table-striped">
          <tr>
            <th>Almacén origen:</th>
            <td><?=$almacenOrigen->nombre?></td>
          </tr>
          <tr>
            <th>Almacén destino:</th>
            <td><?=$almacenDestino->nombre?></td>
          </tr>
          <tr>
            <th>Envía:</th>
            <td><?=$operador->nombre." ".$operador->apaterno." ".$operador->amaterno?></td>
          </tr>
          <tr>
            <th>Recibe:</th>
            <td><?=$operadorRecibe->nombre." ".$operadorRecibe->apaterno." ".$operadorRecibe->amaterno?></td>
          </tr>
          <tr>
            <th>Referencia:</th>
            <td><?=$data->referencia?></td>
          </tr>
        </table>
      </div>
    </div>

    <div class="row px-0">
      <div class="col-12">
        <?if($data->estatus == "pendiente"){?>
          <button class="btn btn-primary float-right" onclick="enviarTransferencia()">Enviar transferencia</button>
        <?}?>
        <?if($data->estatus == "enviada"){?>
          <button class="btn btn-primary float-right" onclick="recibirTransferencia()">Recibir transferencia</button>
          <button class="btn btn-secondary float-right ml-4" onclick="cancelarTransferencia()" style="margin-right: 10px !important;">Cancelar transferencia</button>
        <?}?>
      </div>
    </div>

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
                  <th>Cantidad</th>
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
      <?php if($data->estatus == "pendiente") { ?>
        {
          text: 'Agregar producto',
          className: 'btn-add2',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?transferencia=".$data->id."&tipo=armazon")?>','', '60');
          }
        },
        {
          text: 'Agregar mica',
          className: 'btn-add2',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?transferencia=".$data->id."&tipo=mica")?>','', '60');
          }
        },
        {
          text: 'Agregar extra',
          className: 'btn-add2',
          action: function(e, dt, node, config){
            modalBootStrap('<?=$url("ecom/".$this->interfaz."/agregarProducto?transferencia=".$data->id."&tipo=extra")?>','', '60');
          }
        }
      <?php } ?>
      ],
      "ajax": {
        "url": "<?=$url("ecom/".$this->interfaz."/getAllDetalle?id=".$data->id)?>",
        "dataSrc": ""
      },
      "columns": [
        {"data": "id"},
        {"data": "producto_nombre"},
        {"data": "cantidad"},
        {
          "data": function(row){
            var sHtml = "";
            if(row.estatus == "pendiente"){
              sHtml += "&nbsp;<button class=\"act-btn boton-eliminar\"><i class=\"fas fa-trash\"></i></button>"; 
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
        var url = "<?=$url("ecom/".$this->interfaz."/editar")?>?id=" + data.id;
        window.open(url, '_blank');
      }
      if(button.hasClass("boton-eliminar")){
        eliminaModel(data.id, "<?=$url("ecom/".$this->interfaz."/destroyTransferenciaProducto")?>", [], hDefault);
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

  function enviarTransferencia(){
    Swal.fire({
      title: "¿Terminar la transferencia?",
      text: "Una vez enviada no podrá ser modificada.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar",
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Invoca la función para terminar la compra
        confirmaTerminarCompra();
      } 
      // No es necesario manejar explícitamente la cancelación
    });
  }

  function recibirTransferencia(){
    Swal.fire({
      title: "¿Recibir la transferencia?",
      text: "Una vez recibida no se podrá cancelar.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar",
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Invoca la función para terminar la compra
        confirmaRecibirTransferencia();
      } 
      // No es necesario manejar explícitamente la cancelación
    });
  }

  function cancelarTransferencia(){
    Swal.fire({
      title: "Cancelar la transferencia?",
      text: "Una vez cancelada no se podrá recuperar.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Confirmar",
      cancelButtonText: "Cancelar",
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        // Invoca la función para terminar la compra
        confirmaCancelaTransferencia();
      } 
      // No es necesario manejar explícitamente la cancelación
    });
  }

  function confirmaTerminarCompra(){
    modificaModelYRegresa(<?=$data->id?>, "<?=$url("ecom/".$this->interfaz."/terminaTransferencia")?>", [], hDefault);
  }

  function confirmaRecibirTransferencia(){
    modificaModelYRegresa(<?=$data->id?>, "<?=$url("ecom/".$this->interfaz."/recibeTransferencia")?>", [], hDefault);
  }



  function confirmaCancelaTransferencia(){
    modificaModelYRegresa(<?=$data->id?>, "<?=$url("ecom/".$this->interfaz."/cancelaTransferencia")?>", [], hDefault);
  }
</script>