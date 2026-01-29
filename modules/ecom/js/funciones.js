// Manejadores
function hDefault(bSuccess, oElemento, sMessage) {
  alertMessage((bSuccess ? "Éxito" : "Fallo") + ". Mensaje: " + sMessage, (bSuccess ? "success" : "error") );
  if($("#btn-guardar")){
    $("#btn-guardar").button("reset");
  }
}
function hLogin(bSuccess, oElemento, sMessage) {
  if (bSuccess) { recarga(); }
  else { alertMessage(sMessage); }
}
function hEdit(bSuccess, oElemento, sMessage) {
  
  if (bSuccess) { 
    if ((typeof oElemento) == "string" && oElemento != "") { alertMessage(oElemento, "warning", recarga); }
    else {
      recarga();
    }
  }
  else { alertMessage(sMessage); }
}
function hDelete(bSuccess, oElemento, sMessage) {
  if (!bSuccess) { alertMessage(sMessage, "error", recarga); }
  else {
    recarga();
  }
}

// Vuelve a cargar la página enviando la posición horizontal y vertical donde se encuentra
function recarga() {
  var sURL = new String(window.location);
  sURL = elimina_parametro(sURL, /ph=|pv=/);
  window.location.href = sURL + 
    "ph=" + (document.documentElement.scrollLeft || document.body.scrollLeft) + 
    "&pv=" + (document.documentElement.scrollTop || document.body.scrollTop); // Recarga la página sin datos POST
}

// Función que se utiliza para eliminar parámetros de una dirección URL
// Regresa el URL sin estos parámetros.
// Los parámetros son especificados como una expresión regular.
// Ejemplo si se quiere eliminar el parámetro "p1", la expresión sería /p1=/
// Ejemplo si se quiere eliminar el parámetro "ph" y "pv" la expresión sería /ph=|pv=/
function elimina_parametro(sURL, reParametros) {
  var sURLmodificada = "";
  // Obtenemos dos arreglo aUrl[0] contiene el url base y el aUrl[1] contiene los parámetros GET.
  var aUrl = sURL.split('?');
  sURLmodificada = aUrl[0] + "?";
  // Si existe aUrl[1] entonces buscamos si existen parámetros ph y pv 
  if (aUrl[1]) {
    var aParametro = aUrl[1].split('&');
    // Recorremos el arreglo de los parámetros
    for(var i=0; i < aParametro.length; i++){
      // Si no se trata de ph o pv, regresa el parámetro al URL
      if (aParametro[i].search(reParametros) == -1) { sURLmodificada = sURLmodificada +  aParametro[i] + "&"; }
    }
  }
  return sURLmodificada;
}


// Crea una ventana alternativa con la dirección indicada
function abreVentana(sUrl) {
  window.open(sUrl, '', 'scrollbars=no,status=no,modal=yes,dialog=yes,width=100,height=100');
}

function alertMessage(sMessage, style, callback) {
  // alert(sMessage);
  if (style == undefined) { style = "error"; }
  toastr.options.onHidden = callback;
  switch (style) {
    case "success": toastr.success(sMessage); break;
    case "info":    toastr.info(sMessage); break;
    case "warning": toastr.warning(sMessage); break;
    default: toastr.error(sMessage); break;
  }
}

function hideModal(){
  $( '#myModal' ).modal( 'hide' ).data( 'bs.modal', null );
}

function modalBootStrap(sURL, sCallback, aParametro) {
  // Crea un elemento div con los lugares adecuados para colocar una ventana modal de Bootstrap
  if (document.getElementById("myModal") == undefined) {
    var sHtml = '';
    sHtml += '<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">';
    sHtml +=   '<div class="modal-dialog modal-lg">';
    sHtml +=     '<div class="modal-content" id="modal-edit"></div>';
    sHtml +=   '</div>';
    sHtml += '</div>';
    $(sHtml).appendTo('body');
  }

  var datos = {
    callback: sCallback
  };

  if(aParametro){
    datos["parametro"] = aParametro;
  }
  // Antes de invocar la ventana modal deberíamos mostrar una pantalla de "cargando..."
  $("#modal-edit").load(sURL,
    datos, // Parámetros de Post. Función a la cual llamar con los eventos generados por la edición
    function (responseText, textStatus, XMLHttpRequest) {
      $("#myModal").modal({  show: true });
      // Quitar la clase de "cargando..." en caso de que exista
    }
  );
  $('#myModal').on('hidden.bs.modal', function (e) {
    $('#myModal').remove();
  });
}

function modalBootStrapCallback(sURL, sCallback, aParametro) {
  if (document.getElementById("myModal") == undefined) {
    var sHtml = '';
    sHtml += '<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">';
    sHtml +=   '<div class="modal-dialog modal-lg">';
    sHtml +=     '<div class="modal-content" id="modal-edit"></div>';
    sHtml +=   '</div>';
    sHtml += '</div>';
    $(sHtml).appendTo('body');
  }

  var datos = {
    callback: sCallback
  };

  if (aParametro) {
    datos["parametro"] = aParametro;
  }

  $("#modal-edit").load(
    sURL,
    datos,
    function (responseText, textStatus, XMLHttpRequest) {
      $("#myModal").modal({ show: true });
    }
  );

  window.modalCallback = function(data) {
    if (typeof sCallback === "function") {
      sCallback(data); // Pasa los datos del vendedor al callback
    }
  };

  $('#myModal').on('hidden.bs.modal', function () {
    $('#myModal').remove();
    delete window.modalCallback; // Limpia el callback para evitar conflictos
  });
}

function modalBootStrapProducto(sURL, sCallback, aParametro) {
  // Crea un elemento div con los lugares adecuados para colocar una ventana modal de Bootstrap
  if (document.getElementById("myModal") == undefined) {
    var sHtml = '';
    sHtml += '<div class="modal inmodal" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
    sHtml +=   '<div class="modal-dialog modal-lg" role="document">';
    sHtml +=     '<div class="modal-content" id="modal-edit"></div>';
    sHtml +=   '</div>';
    sHtml += '</div>';
    $(sHtml).appendTo('body');
  }

  var datos = {
    callback: sCallback
  };

  if(aParametro){
    datos["parametro"] = aParametro;
  }
  // Antes de invocar la ventana modal deberíamos mostrar una pantalla de "cargando..."
  $("#modal-edit").load(sURL,
    datos, // Parámetros de Post. Función a la cual llamar con los eventos generados por la edición
    function (responseText, textStatus, XMLHttpRequest) {
      $("#myModal").modal({  show: true });
      // Quitar la clase de "cargando..." en caso de que exista
    }
  );
  $('#myModal').on('hidden.bs.modal', function (e) {
    $('#myModal').remove();
  });
}


function modalBootStrapCotizador(sURL, sCallback, aParametro) {
  // Crea un elemento div con los lugares adecuados para colocar una ventana modal de Bootstrap
  if (document.getElementById("myModal") == undefined) {
    var sHtml = '';
    sHtml += '<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
    sHtml +=   '<div class="modal-dialog modal-lg" role="document">';
    sHtml +=     '<div class="modal-content" id="modal-edit"></div>';
    sHtml +=   '</div>';
    sHtml += '</div>';
    $(sHtml).appendTo('body');
  }

  var datos = {
    callback: sCallback
  };

  if(aParametro){
    datos["parametro"] = aParametro;
  }
  // Antes de invocar la ventana modal deberíamos mostrar una pantalla de "cargando..."
  $("#modal-edit").load(sURL,
    datos, // Parámetros de Post. Función a la cual llamar con los eventos generados por la edición
    function (responseText, textStatus, XMLHttpRequest) {
      $("#myModal").modal({  show: true });
      //cargaProductos();
    }
  );
  $('#myModal').on('hidden.bs.modal', function (e) {
    $('#myModal').remove();
  });
}

function modalBootStrapDemo(sURL, aParametros) {
  // Crea un elemento div con los lugares adecuados para colocar una ventana modal de Bootstrap
  if (document.getElementById("myModal") == undefined) {
    var sHtml = '';
    sHtml += '<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
    sHtml +=   '<div class="modal-dialog modal-lg" role="document">';
    sHtml +=     '<div class="modal-content" id="modal-edit"></div>';
    sHtml +=   '</div>';
    sHtml += '</div>';
    $(sHtml).appendTo('body');
  }

  var datos = {
    "data": aParametros
  };

  // Antes de invocar la ventana modal deberíamos mostrar una pantalla de "cargando..."
  $("#modal-edit").load(sURL,
    datos, // Parámetros de Post. Función a la cual llamar con los eventos generados por la edición
    function (responseText, textStatus, XMLHttpRequest) {
      $("#myModal").modal({  show: true });
      // Quitar la clase de "cargando..." en caso de que exista
    }
  );
  $('#myModal').on('hidden.bs.modal', function (e) {
    $('#myModal').remove();
  });
}

// Envía los datos de un formulario de edición,
// obtienen la respuesta e invoca a la función "callback" una vez que se obtuvo la respuesta
function enviaFormularioModel(sFormId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var $inputs = $("#" + sFormId + " :input");
    var values = {};
    $inputs.each(function() { 
      //alertMessage(this.name + " " + $(this).prop("type") + ":" + $(this).val());
      switch($(this).prop("type")) {
        case "radio":
          if ($(this)[0].checked) { values[this.name] = $(this).val(); }
          break;
        case "checkbox":

          if ($(this).prop("checked")) { values[this.name] = $(this).val(); }
          else {values[this.name] = ""}
          break;
        default:
          values[this.name] = $(this).val();
      }
    });
    
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.message; }
        if (!data.id) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizaron los datos");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

// Envía los datos de un formulario de edición,
// obtienen la respuesta e invoca a la función "callback" una vez que se obtuvo la respuesta
function enviaFormularioModelProducto2(sFormId, sUrl, objeto, callback, pagina) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var $inputs = $("#" + sFormId + " :input");
    var values = {};
    $inputs.each(function() { 
      //alertMessage(this.name + " " + $(this).prop("type") + ":" + $(this).val());
      switch($(this).prop("type")) {
        case "radio":
          if ($(this)[0].checked) { values[this.name] = $(this).val(); }
          break;
        case "checkbox":

          if ($(this).prop("checked")) { values[this.name] = $(this).val(); }
          else {values[this.name] = ""}
          break;
        default:
          values[this.name] = $(this).val();
      }
    });
    
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.message; }
        if (!data.id) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizaron los datos");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();
        if(pagina != null){
          $('#tabla-data').DataTable().page(pagina);
        }
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

// Envía los datos de un formulario de edición,
// obtienen la respuesta e invoca a la función "callback" una vez que se obtuvo la respuesta
function enviaFormularioModelProducto(sFormId, sUrl, objeto, callback, pagina) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var $inputs = $("#" + sFormId + " :input");
    var values = {};
    $inputs.each(function() { 
      // alertMessage(this.name + " " + $(this).prop("type") + ":" + $(this).val());
      switch($(this).prop("type")) {
        case "radio":
          if ($(this)[0].checked) { values[this.name] = $(this).val(); }
          break;
        default:
          values[this.name] = $(this).val();
      }
    });
    
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.message; }
        if (!data.id) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        //callback(true, objeto, "Se actualizaron los datos");
        //hideModal();
        $('#tabla-data').DataTable().ajax.reload();
      
        //subeDistintivo(data.id);
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

// Envía los datos de un formulario de edición,
// obtienen la respuesta e invoca a la función "callback" una vez que se obtuvo la respuesta
function eliminaModel(sModelId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var values = {
      id: sModelId
    };
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.error; }
        if (data.error) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se eliminó el objeto");
        hideModal();
        $('#tabla-data').DataTable().ajax.reload();
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

// Envía los datos de un formulario de edición,
// obtienen la respuesta e invoca a la función "callback" una vez que se obtuvo la respuesta
function modificaModel(sModelId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var values = {
      id: sModelId
    };
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.error; }
        if (data.error) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizó la información");
        $('#tabla-data').DataTable().ajax.reload();
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

function modificaModelCantidad(sModelId, sCantidad, sCompra, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var values = {
      id: sModelId,
      cantidad: sCantidad,
      compra: sCompra
    };
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.error != undefined) { objeto = data.error; }
        if (data.error) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizó la información");
        $('#tabla-data').DataTable().ajax.reload();
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

function modificaModelYRegresa(sModelId, sUrl, objeto, callback) {
  // Obtiene los valores de todos los campos dentro del formulario
  // La siguiente instrucción también obtiene los valores de los campos select
  var values = {
      id: sModelId
  };
  // Envía la información y espera un JSON como respuesta.
  //
  var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
          console.log(data);

          $("#btn-guardar").button('reset');
          // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
          // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
          
          if (data == undefined) {
              callback(false, objeto, "Error en la respuesta de la función invocada. No se regresó ninguna información.\n");
              return;
          }
          if (data.error != undefined) { objeto = data.error; }
          if (data.error) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
          callback(true, objeto, "Se actualizó la información");
          window.history.back();
      }
  )
  .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log(jqXHR);
  });
}

function hideModal(){
  $( '#myModal' ).modal( 'hide' ).data( 'bs.modal', null );
}

function enviaFormularioCliente(sFormId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var $inputs = $("#" + sFormId + " :input");
    var values = {};
    $inputs.each(function() { 
      // alertMessage(this.name + " " + $(this).prop("type") + ":" + $(this).val());
      switch($(this).prop("type")) {
        case "radio":
          if ($(this)[0].checked) { values[this.name] = $(this).val(); }
          break;
        default:
          values[this.name] = $(this).val();
      }
    });
    
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.message != undefined) { objeto = data.message; }
        if (!data.success) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizaron los datos");
        window.location.reload();
        console.log( data );
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

function enviaFormularioCotizacion(sFormId, sUrl, objeto, callback) {
    // Obtiene los valores de todos los campos dentro del formulario
    // La siguiente instrucción también obtiene los valores de los campos select
    var $inputs = $("#" + sFormId + " :input");
    var values = {};
    $inputs.each(function() { 
      // alertMessage(this.name + " " + $(this).prop("type") + ":" + $(this).val());
      switch($(this).prop("type")) {
        case "radio":
          if ($(this)[0].checked) { values[this.name] = $(this).val(); }
          break;
        default:
          values[this.name] = $(this).val();
      }
    });
    
    // Envía la información y espera un JSON como respuesta.
    //
    var jqxhr = $.getJSON(
      sUrl,
      values,
      function(data, textStatus, jqXHR) {
        console.log( data );

        $("#btn-guardar").button('reset');
        // En teoría texStatus siempre debería ser "success", ya que está la función "fail", que se encarga de manejear eventos de error.
        // Si no hay objeto de datos, no se puede saber si se actualizaron o no los datos
        
        if (data == undefined) {
          callback(false, objeto, "Error en el la respuesta de la función de invocada. No sé regresó ninguna información.\n");
          return;
        }
        if (data.message != undefined) { objeto = data.message; }
        if (!data.id) { callback(false, objeto, "Error al actualizar los datos.\n" + (data.error ? data.error : "")); return; }
        callback(true, objeto, "Se actualizaron los datos");
        window.location = "ecom/cotizador/reporte";
        console.log( data );
      }
    )
    .fail(function(jqXHR, textStatus, errorThrown) {
      // Se invoca cuando la petición Ajax no pudo completarse
      // o cuando no se pudo interpretar adecuadamente la respuesta
      // alertMessage(textStatus);
      // alertMessage(errorThrown);
      callback(false, objeto, "Error al invocar la función de actualización de datos:\n" + textStatus + "\n" + errorThrown);
      console.log( jqXHR );
    });
}

