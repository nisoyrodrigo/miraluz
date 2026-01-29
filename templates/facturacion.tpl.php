<section id="subheader" class="bg-color-op-1">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="col-lg-12">
                <h1 class="split">Solicitar Factura</h1>
                <ul class="crumb wow fadeInUp">
                    <li><a href="<?=$url("seguimiento")?>">Centro de Seguimiento</a></li>
                    <li class="active">Facturación</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="relative pt-5 pb-5">
  <div class="container relative z-2">
    <div class="row justify-content-center">

      <div class="col-lg-6 col-md-8">

        <div class="p-4 rounded-1 bg-white shadow-sm">

          <h3 class="mb-3 text-center">Solicitar factura</h3>
          <p class="text-center mb-4">
            Ingresa tus datos fiscales y la información de compra.  
            Nuestro equipo procesará tu solicitud y te enviará la factura por correo.
          </p>

          <!-- FORMULARIO -->
          <form id="formFactura" enctype="multipart/form-data">

            <!-- Número de venta -->
            <div class="mb-3">
              <label class="form-label">Número de nota</label>
              <input type="text" name="venta" class="form-control" required>
            </div>

            <!-- Clave -->
            <div class="mb-3">
              <label class="form-label">Clave</label>
              <input type="password" name="clave" class="form-control" required>
            </div>

            <!-- RFC -->
            <div class="mb-3">
              <label class="form-label">RFC</label>
              <input type="text" name="rfc" class="form-control" maxlength="13" required>
            </div>

            <!-- Razón Social -->
            <div class="mb-3">
              <label class="form-label">Razón Social</label>
              <input type="text" name="razon" class="form-control" required>
            </div>

            <!-- Correo -->
            <div class="mb-3">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="correo" class="form-control" required>
            </div>

            <!-- Uso de CFDI -->
            <div class="mb-4">
              <label class="form-label">Uso de CFDI</label>
              <select name="cfdi" class="form-select" required>
                <option value="">Selecciona una opción</option>
                <option value="G01">G01 - Adquisición de mercancías</option>
                <option value="G03">G03 - Gastos en general</option>
                <option value="P01">P01 - Por definir</option>
                <option value="D01">D01 - Honorarios médicos</option>
                <option value="D02">D02 - Gastos médicos</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Régimen fiscal</label>
              <select name="regimen_fiscal" class="form-select" required>
                <option value="">Selecciona tu régimen fiscal</option>

                <!-- PERSONAS FÍSICAS -->
                <option value="605">605 - Sueldos y Salarios e Ingresos Asimilados</option>
                <option value="606">606 - Arrendamiento</option>
                <option value="608">608 - Demás ingresos</option>
                <option value="610">610 - Residentes en el Extranjero sin EP en México</option>
                <option value="611">611 - Ingresos por Dividendos</option>
                <option value="612">612 - Personas Físicas con Actividades Empresariales</option>
                <option value="614">614 - Ingresos por Intereses</option>
                <option value="615">615 - Régimen de los ingresos por obtención de premios</option>
                <option value="616">616 - Sin obligaciones fiscales</option>
                <option value="621">621 - Incorporación Fiscal</option>
                <option value="625">625 - Régimen de las Actividades Empresariales con ingresos a través de Plataformas Tecnológicas</option>
                <option value="626">626 - Régimen Simplificado de Confianza (RESICO PF)</option>

                <!-- PERSONAS MORALES -->
                <option value="601">601 - General de Ley Personas Morales</option>
                <option value="603">603 - Personas Morales con Fines no Lucrativos</option>
                <option value="620">620 - Sociedades Cooperativas de Producción</option>
                <option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras</option>
                <option value="623">623 - Opcional para Grupos de Sociedades</option>
                <option value="624">624 - Coordinados</option>
                <option value="628">628 - Hidrocarburos</option>
                <option value="630">630 - Enajenación de acciones en bolsa de valores</option>
              </select>
            </div>


            <div class="mb-3">
              <label class="form-label">Dirección fiscal</label>
              <input 
                type="text" 
                name="direccion_fiscal" 
                class="form-control" 
                placeholder="Calle, número, colonia"
                required
              >
            </div>

            <div class="mb-3">
              <label class="form-label">Código postal</label>
              <input 
                type="text" 
                name="codigo_postal" 
                class="form-control" 
                maxlength="5"
                pattern="[0-9]{5}"
                placeholder="00000"
                required
              >
            </div>

            <div class="mb-4">
              <label class="form-label">Constancia de situación fiscal (PDF)</label>
              <input 
                type="file"
                name="constancia_fiscal"
                class="form-control"
                accept="application/pdf"
                required
              >
              <small class="text-muted">
                Solo archivos PDF
              </small>
            </div>

            <div class="mb-4">
              <label class="form-label">Observaciones</label>
              <textarea 
                name="observaciones" 
                class="form-control" 
                rows="3"
                placeholder="Algún comentario adicional (opcional)"
              ></textarea>
            </div>



            <!-- Botón -->
            <div class="text-center">
              <button type="submit" class="btn-main" id="btnFactura">
                Enviar solicitud
              </button>
            </div>

          </form>

          <!-- Resultado -->
          <div id="resultadoFactura" class="mt-4" style="display:none;"></div>

        </div>

      </div>

    </div>
  </div>
</section>


<script>
$(document).ready(function(){

  $("#formFactura").submit(function(e){
      e.preventDefault();

      let form = $(this);
      let btn = $("#btnFactura");
      let resultado = $("#resultadoFactura");

      btn.prop("disabled", true).text("Enviando...");
      resultado.hide().html("");

      $.ajax({
        url: "<?=$url("web/default/solicitarfactura")?>",
        type: "POST",
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: "json",

        success: function(res){
            btn.prop("disabled", false).text("Enviar solicitud");

            if (!res.exito) {
                resultado
                  .html(`<div class="alert alert-danger">${res.error ?? "No se pudo enviar la solicitud"}</div>`)
                  .show();
                return;
            }

            resultado.html(`
              <div class="alert alert-success">
                  <strong>Solicitud enviada correctamente.</strong><br>
                  Nuestro equipo procesará tu factura y te la enviará al correo proporcionado.
              </div>
            `).show();

            form.trigger("reset");
        },

        error: function(){
            btn.prop("disabled", false).text("Enviar solicitud");
            resultado
              .html(`
                <div class="alert alert-danger">
                    Error al conectar con el servidor. Intenta más tarde.
                </div>
              `)
              .show();
        }
      });

  });

});
</script>
