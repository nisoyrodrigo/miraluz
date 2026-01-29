<?php
$noImage = $urlm("images/image-holder.jpg");
$clientes = Cliente::model()->findAll("WHERE estatus = 1");
$tipos = TipoProducto::model()->findAll("WHERE estatus = 1");
?>




  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
    <i class="fa fa-pencil modal-icon"></i>
    <h4 class="modal-title">Seleccionar cliente</h4>
  </div>

  <div class="modal-body">

    <div class="row">

      <div class="col-4">

        <form id="formEdit" class="form-horizontal" method="POST" action="<?=$url("ecom/cliente/save")?>" enctype="multipart/form-data">

          <div class="form-group row">
            <label class="col-form-label">Nombre</label>
            <input type="text" class="form-control" name="nombre" id="anombre" required>
          </div>

          <div class="form-group row">
            <label class="col-form-label">Fecha de nacimiento</label>
            <input type="date" class="form-control" name="fecha_nacimiento" id="afecha_nacimiento">
          </div>

          <div class="form-group row">
            <label class="col-form-label">Teléfono</label>
            <input type="text" class="form-control" name="telefono" id="atelefono">
          </div>

          <div class="form-group row">
            <label class="col-form-label">Correo</label>
            <input type="text" class="form-control" name="correo" id="acorreo">
          </div>

          <div class="form-group row">
            <button id="btn-guardar" type="button" class="btn btn-primary" onclick="guardaCliente();">Guardar</button>
          </div>

        </form>
        
      </div>

      <div class="col-8">
        
        <input type="hidden" name="id" value="<?=$data->id?>" />
        <div class="form-group">
          <div id="custom-search-input">
            <label class="col-form-label">&nbsp;</label>
            <div class="input-group col-md-12">
              <input type="text" id="inputBuscar" class="form-control input-lg" placeholder="Buscar" />
              <span class="input-group-btn">
                <button class="btn btn-info btn-lg" type="button" onclick="buscarCliente()">
                    <i class="glyphicon glyphicon-search"></i>
                </button>
              </span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-6 mb-4">
            <div class="card-cliente">
              <h4><i class="fas fa-user"></i> CLIENTE MOSTRADOR</h4><br>
              <h5><i class="fas fa-phone"></i> 00 0000 0000</h5>
              <h5><i class="fas fa-envelope"></i> optica.miraluz@gmail.com</h5><br><br>
              <button type="button" class="btn btn-primary float-end" onclick="seleccionarCliente(1, 'CLIENTE MOSTRADOR')">Seleccionar</button>
            </div>
          </div>
        </div>

        <div id="modalClientes" class="row">
          
        </div>

      </div>


    </div>
      
      
  </div>


 <script type="text/javascript">
  $(document).ready(function(){
    
    $("#anombre, #atelefono, #acorreo").on("change", function() {
      if ($("#anombre").val().length > 5 || $("#atelefono").val().length > 5 || $("#acorreo").val().length > 5) {
        buscarClienteAlt();
      }
    });

  });

  function guardaCliente(){
    console.log('guardaCliente');
    const formData = {
      nombre: $('input[name="nombre"]').val(),
      fecha_nacimiento: $('input[name="fecha_nacimiento"]').val(),
      telefono: $('input[name="telefono"]').val(),
      correo: $('input[name="correo"]').val()
    };

    if(formData.telefono === "" || formData.correo === ""){
      alertMessage("Debes registrar por lo menos un medio de contacto.");
      return;
    }

    $.post('<?=$url("ecom/venta/verificarDuplicado")?>', { telefono: formData.telefono, correo: formData.correo })
    .done(function(response) {
      console.log(response);

      // Verificar si hay un error en la respuesta
      if (response.error) {
        alertMessage(`Error: ${response.error}`);
        return; // Detener el flujo aquí
      }

      if (response.duplicados && response.duplicados.length > 0) {
        // Mostrar los duplicados en un Swal
        Swal.fire({
          title: 'Cliente(s) duplicado(s) encontrado(s)',
          html: generarTablaDuplicados(response.duplicados),
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, duplicar',
          cancelButtonText: 'No',
          customClass: {
            popup: 'swal-custom'
          }
        }).then((result) => {
          if (result.isConfirmed) {
            // Si acepta, guardar el cliente
            guardaClienteS2(formData);
          } else {
            console.log('Usuario canceló la acción.');
          }
        });
      } else {
        // Si no hay duplicados, guardar directamente
        console.log('No encontró duplicados');
        guardaClienteS2(formData);
      }
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      console.error('Error en la solicitud:', textStatus, errorThrown);
      alertMessage("Ocurrió un error al verificar duplicados. Por favor, verifica tu conexión o contacta al administrador.");
    });
   
  }

  function guardaClienteS2(formData){
    $.post('<?=$url("ecom/cliente/save")?>', formData)
    .done(function(response) {
      $("#btn-guardar").html("Guardar");
      if (response.error === undefined) {
        alertMessage("Información guardada satisfactoriamente.", "success");
        console.log('dato almacenado');
        console.log(response);
        console.log('idEmpleado');
        console.log(response.id);
        window.parent.document.getElementById('nombreCliente').value = formData.nombre;
        window.parent.document.getElementById('ventaClienteId').value = response.id;
        hideModal();
      } else {
        alertMessage(response.error, "danger");
        console.log("Error al guardar información");
      }
    })
    .fail(function() {
      alertMessage("Ocurrió un error al guardar.", "danger");
    });
  }

  function buscarCliente(){
    const query = $('#inputBuscar').val();

    if (query.trim() === '') {
      $('#modalClientes').html('<p class="text-warning">Por favor, ingresa un criterio de búsqueda.</p>');
      return;
    }
    $.post('<?=$url("ecom/".$this->interfaz."/buscarCliente")?>', { query: query })
    .done(function(response) {
      // Inyectar el HTML de la respuesta directamente en el div `#modalClientes`
      $('#modalClientes').html(response);
    })
    .fail(function() {
      $('#modalClientes').html('<p class="text-danger">Ocurrió un error al realizar la búsqueda. Inténtalo de nuevo.</p>');
    });
  }


  function buscarClienteAlt(){
    console.log("buscar cliente alt");
    const anombre = $("#anombre").val().trim();
    const atelefono = $("#atelefono").val().trim();
    const acorreo = $("#acorreo").val().trim();
    const data = { nombre: anombre, telefono: atelefono, correo: acorreo };
    console.log(data)
    $.post('<?=$url("ecom/".$this->interfaz."/buscarClienteAlt")?>', data)
    .done(function(response) {
      console.log(response);
      // Inyectar el HTML de la respuesta directamente en el div `#modalClientes`
      $('#modalClientes').html(response);
    })
    .fail(function() {
      $('#modalClientes').html('<p class="text-danger">Ocurrió un error al realizar la búsqueda. Inténtalo de nuevo.</p>');
    });
  }

  function seleccionarCliente(id, nombre){
    window.parent.document.getElementById('nombreCliente').value = nombre;
    window.parent.document.getElementById('ventaClienteId').value = id;
    hideModal();
  }


  // Función para generar la tabla con los datos de duplicados
  function generarTablaDuplicados(duplicados) {
      if (duplicados.length === 0) {
          return '<p>No se encontraron duplicados.</p>';
      }

      // Crear encabezados de la tabla
      let tablaHtml = `
          <table style="width:100%; border-collapse: collapse; text-align: left;">
              <thead>
                  <tr>
                      <th style="border-bottom: 1px solid #ddd; padding: 8px;">Nombre</th>
                      <th style="border-bottom: 1px solid #ddd; padding: 8px;">Teléfono</th>
                      <th style="border-bottom: 1px solid #ddd; padding: 8px;">Correo</th>
                      <th style="border-bottom: 1px solid #ddd; padding: 8px;">Fecha de creación</th>
                  </tr>
              </thead>
              <tbody>
      `;

      // Agregar las filas de la tabla
      duplicados.forEach(cliente => {
          tablaHtml += `
              <tr>
                  <td style="border-bottom: 1px solid #ddd; padding: 8px;">${cliente.nombre}</td>
                  <td style="border-bottom: 1px solid #ddd; padding: 8px;">${cliente.telefono}</td>
                  <td style="border-bottom: 1px solid #ddd; padding: 8px;">${cliente.correo}</td>
                  <td style="border-bottom: 1px solid #ddd; padding: 8px;">${cliente.created}</td>
              </tr>
          `;
      });

      // Cerrar la tabla
      tablaHtml += `
              </tbody>
          </table>
      `;

      return tablaHtml;
  }
 </script>