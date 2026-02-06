<!-- SUBHEADER -->
<section class="ml-section" style="padding: 140px 40px 60px; background: linear-gradient(180deg, var(--ml-dark) 0%, var(--ml-dark-card) 100%);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <div class="ml-section-tag">Agenda tu Cita</div>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 16px;">Reserva tu Examen Visual</h1>
    <p style="color: var(--ml-text-secondary); max-width: 600px; margin: 0 auto;">
      Agenda en línea y recibe un descuento especial en tu próxima compra.
    </p>
  </div>
</section>

<!-- CITAS -->
<section class="ml-section" style="padding: 80px 40px;">
  <div style="max-width: 1100px; margin: 0 auto;">
    <div class="ml-citas-container">
      <!-- Info -->
      <div class="ml-citas-info ml-reveal">
        <h3>Tu visión merece la mejor atención</h3>
        <p>Al agendar tu cita en línea, recibes un descuento automático del 10% en tu siguiente compra. Nuestros optometristas certificados te atenderán con equipo de última generación.</p>
        <div class="ml-citas-features">
          <div class="ml-cita-feature">
            <div class="check"><i class="fas fa-check"></i></div>
            <span>Examen visual completo (20-30 min)</span>
          </div>
          <div class="ml-cita-feature">
            <div class="check"><i class="fas fa-check"></i></div>
            <span>Topografía corneal incluida</span>
          </div>
          <div class="ml-cita-feature">
            <div class="check"><i class="fas fa-check"></i></div>
            <span>Asesoría personalizada en armazones</span>
          </div>
          <div class="ml-cita-feature">
            <div class="check"><i class="fas fa-check"></i></div>
            <span>10% de descuento por agendar en línea</span>
          </div>
          <div class="ml-cita-feature">
            <div class="check"><i class="fas fa-check"></i></div>
            <span>Recordatorio por correo y WhatsApp</span>
          </div>
        </div>
        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&q=80" alt="Examen visual" style="width:100%;border-radius:var(--ml-radius-lg);margin-top:24px;opacity:0.9">
      </div>

      <!-- Formulario -->
      <div class="ml-citas-form ml-reveal ml-reveal-delay-1">
        <h3>Llena tus datos</h3>
        <form id="formCita">
          <div class="ml-form-row">
            <div class="ml-form-group">
              <label>Nombre completo</label>
              <input type="text" name="nombre" placeholder="Juan Pérez" required>
            </div>
            <div class="ml-form-group">
              <label>Teléfono</label>
              <input type="tel" name="telefono" placeholder="55 1234 5678" required>
            </div>
          </div>
          <div class="ml-form-group">
            <label>Correo electrónico</label>
            <input type="email" name="correo" placeholder="tu@correo.com" required>
          </div>
          <div class="ml-form-group">
            <label>Sucursal</label>
            <select name="sucursal" required>
              <option value="">Selecciona sucursal</option>
              <option value="centro">Sucursal Centro</option>
              <option value="plaza_norte">Sucursal Plaza Norte</option>
              <option value="valle">Sucursal Valle</option>
            </select>
          </div>
          <div class="ml-form-row">
            <div class="ml-form-group">
              <label>Fecha</label>
              <input type="date" name="fecha" required>
            </div>
            <div class="ml-form-group">
              <label>Horario preferido</label>
              <select name="horario" required>
                <option value="">Selecciona horario</option>
                <option value="09:00">9:00 - 10:00</option>
                <option value="10:00">10:00 - 11:00</option>
                <option value="11:00">11:00 - 12:00</option>
                <option value="12:00">12:00 - 13:00</option>
                <option value="16:00">16:00 - 17:00</option>
                <option value="17:00">17:00 - 18:00</option>
                <option value="18:00">18:00 - 19:00</option>
              </select>
            </div>
          </div>
          <div class="ml-form-group">
            <label>¿Es tu primera visita?</label>
            <select name="primera_visita">
              <option value="si">Sí, es mi primera vez</option>
              <option value="no">No, ya soy cliente</option>
            </select>
          </div>
          <button type="submit" class="ml-btn-primary" id="btnCita" style="width:100%;justify-content:center;margin-top:8px">
            <span>Agendar Cita</span> <i class="fas fa-calendar-check"></i>
          </button>
        </form>

        <!-- Resultado -->
        <div id="resultadoCita" style="display: none; margin-top: 24px;"></div>
      </div>
    </div>
  </div>
</section>

<script>
$(document).ready(function(){
  $("#formCita").submit(function(e){
    e.preventDefault();

    let form = $(this);
    let btn = $("#btnCita");
    let resultado = $("#resultadoCita");

    btn.prop("disabled", true).find("span").text("Agendando...");
    resultado.hide().html("");

    $.ajax({
      url: "<?=$url("web/default/agendarcita")?>",
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: function(res){
        btn.prop("disabled", false).find("span").text("Agendar Cita");

        if (!res.exito) {
          resultado.html(`
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #ef4444;">
              <i class="fas fa-exclamation-circle"></i> ${res.error || "No se pudo agendar la cita"}
            </div>
          `).show();
          return;
        }

        resultado.html(`
          <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #22c55e;">
            <i class="fas fa-check-circle"></i> <strong>¡Cita agendada correctamente!</strong><br>
            <span style="color: var(--ml-text-secondary);">Te enviaremos un recordatorio por correo y WhatsApp.</span>
          </div>
        `).show();

        form.trigger("reset");
      },
      error: function(){
        btn.prop("disabled", false).find("span").text("Agendar Cita");
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
