<!-- SUBHEADER -->
<section class="ml-section" style="padding: 140px 40px 60px; background: linear-gradient(180deg, var(--ml-dark) 0%, var(--ml-dark-card) 100%);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <div class="ml-section-tag">Facturación</div>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 16px;">Solicitar Factura</h1>
    <nav style="display: flex; justify-content: center; gap: 8px; color: var(--ml-text-muted); font-size: 0.9rem;">
      <a href="<?=$url("seguimiento")?>" style="color: var(--ml-green-primary); text-decoration: none;">Centro de Seguimiento</a>
      <span>/</span>
      <span>Facturación</span>
    </nav>
  </div>
</section>

<!-- FORMULARIO -->
<section class="ml-section" style="padding: 80px 40px;">
  <div style="max-width: 600px; margin: 0 auto;">
    <div class="ml-citas-form">
      <h3 style="text-align: center; margin-bottom: 8px;">Datos de facturación</h3>
      <p style="text-align: center; color: var(--ml-text-secondary); margin-bottom: 24px;">
        Ingresa tus datos fiscales. Te enviaremos la factura por correo.
      </p>

      <form id="formFactura" enctype="multipart/form-data">
        <!-- Datos de compra -->
        <div class="ml-form-row">
          <div class="ml-form-group">
            <label>Número de nota</label>
            <input type="text" name="venta" placeholder="Ej: 1234" required>
          </div>
          <div class="ml-form-group">
            <label>Clave</label>
            <input type="password" name="clave" placeholder="Tu clave" required>
          </div>
        </div>

        <!-- RFC y Razón Social -->
        <div class="ml-form-row">
          <div class="ml-form-group">
            <label>RFC</label>
            <input type="text" name="rfc" maxlength="13" placeholder="XAXX010101000" required style="text-transform: uppercase;">
          </div>
          <div class="ml-form-group">
            <label>Razón Social</label>
            <input type="text" name="razon" placeholder="Nombre o empresa" required>
          </div>
        </div>

        <!-- Correo -->
        <div class="ml-form-group">
          <label>Correo electrónico</label>
          <input type="email" name="correo" placeholder="tu@correo.com" required>
        </div>

        <!-- Uso CFDI -->
        <div class="ml-form-group">
          <label>Uso de CFDI</label>
          <select name="cfdi" required>
            <option value="">Selecciona una opción</option>
            <option value="G01">G01 - Adquisición de mercancías</option>
            <option value="G03">G03 - Gastos en general</option>
            <option value="D01">D01 - Honorarios médicos</option>
            <option value="D02">D02 - Gastos médicos</option>
            <option value="P01">P01 - Por definir</option>
          </select>
        </div>

        <!-- Régimen fiscal -->
        <div class="ml-form-group">
          <label>Régimen fiscal</label>
          <select name="regimen_fiscal" required>
            <option value="">Selecciona tu régimen</option>
            <optgroup label="Personas Físicas">
              <option value="605">605 - Sueldos y Salarios</option>
              <option value="606">606 - Arrendamiento</option>
              <option value="612">612 - Actividades Empresariales</option>
              <option value="616">616 - Sin obligaciones fiscales</option>
              <option value="626">626 - RESICO</option>
            </optgroup>
            <optgroup label="Personas Morales">
              <option value="601">601 - General de Ley PM</option>
              <option value="603">603 - PM Fines no Lucrativos</option>
            </optgroup>
          </select>
        </div>

        <!-- Dirección y CP -->
        <div class="ml-form-group">
          <label>Dirección fiscal</label>
          <input type="text" name="direccion_fiscal" placeholder="Calle, número, colonia" required>
        </div>

        <div class="ml-form-row">
          <div class="ml-form-group">
            <label>Código postal</label>
            <input type="text" name="codigo_postal" maxlength="5" pattern="[0-9]{5}" placeholder="00000" required>
          </div>
          <div class="ml-form-group">
            <label>Constancia fiscal (PDF)</label>
            <input type="file" name="constancia_fiscal" accept="application/pdf" required style="padding: 10px;">
          </div>
        </div>

        <!-- Observaciones -->
        <div class="ml-form-group">
          <label>Observaciones (opcional)</label>
          <textarea name="observaciones" rows="3" placeholder="Algún comentario adicional"></textarea>
        </div>

        <button type="submit" class="ml-btn-primary" id="btnFactura" style="width: 100%; justify-content: center; margin-top: 8px;">
          <span>Enviar solicitud</span> <i class="fas fa-paper-plane"></i>
        </button>
      </form>

      <!-- Resultado -->
      <div id="resultadoFactura" style="display: none; margin-top: 24px;"></div>
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

    btn.prop("disabled", true).find("span").text("Enviando...");
    resultado.hide().html("");

    $.ajax({
      url: "<?=$url("web/default/solicitarfactura")?>",
      type: "POST",
      data: new FormData(this),
      processData: false,
      contentType: false,
      dataType: "json",
      success: function(res){
        btn.prop("disabled", false).find("span").text("Enviar solicitud");

        if (!res.exito) {
          resultado.html(`
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #ef4444;">
              <i class="fas fa-exclamation-circle"></i> ${res.error || "No se pudo enviar la solicitud"}
            </div>
          `).show();
          return;
        }

        resultado.html(`
          <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #22c55e;">
            <i class="fas fa-check-circle"></i> <strong>Solicitud enviada correctamente.</strong><br>
            <span style="color: var(--ml-text-secondary);">Te enviaremos la factura al correo proporcionado.</span>
          </div>
        `).show();

        form.trigger("reset");
      },
      error: function(){
        btn.prop("disabled", false).find("span").text("Enviar solicitud");
        resultado.html(`
          <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #ef4444;">
            <i class="fas fa-exclamation-circle"></i> Error al conectar. Intenta más tarde.
          </div>
        `).show();
      }
    });
  });
});
</script>
