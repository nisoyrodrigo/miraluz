<!-- CITAS -->
<section class="ml-section" id="ml-citas">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Agenda tu Cita</div>
    <h2>Reserva tu examen visual</h2>
    <p>Agenda en línea y recibe un descuento especial en tu próxima compra</p>
  </div>

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
    </div>

    <!-- Formulario -->
    <div class="ml-citas-form ml-reveal ml-reveal-delay-2">
      <h3>Llena tus datos</h3>
      <div class="ml-form-row">
        <div class="ml-form-group">
          <label>Nombre completo</label>
          <input type="text" placeholder="Juan Pérez">
        </div>
        <div class="ml-form-group">
          <label>Teléfono</label>
          <input type="tel" placeholder="55 1234 5678">
        </div>
      </div>
      <div class="ml-form-group">
        <label>Correo electrónico</label>
        <input type="email" placeholder="tu@correo.com">
      </div>
      <div class="ml-form-group">
        <label>Sucursal</label>
        <select>
          <option>Selecciona sucursal</option>
          <option>Sucursal Centro</option>
          <option>Sucursal Plaza Norte</option>
          <option>Sucursal Valle</option>
        </select>
      </div>
      <div class="ml-form-row">
        <div class="ml-form-group">
          <label>Fecha</label>
          <input type="date">
        </div>
        <div class="ml-form-group">
          <label>Horario preferido</label>
          <select>
            <option>Selecciona horario</option>
            <option>9:00 - 10:00</option>
            <option>10:00 - 11:00</option>
            <option>11:00 - 12:00</option>
            <option>12:00 - 13:00</option>
            <option>16:00 - 17:00</option>
            <option>17:00 - 18:00</option>
            <option>18:00 - 19:00</option>
          </select>
        </div>
      </div>
      <div class="ml-form-group">
        <label>¿Es tu primera visita?</label>
        <select>
          <option>Sí, es mi primera vez</option>
          <option>No, ya soy cliente</option>
        </select>
      </div>
      <a href="<?=$url("citas")?>" class="ml-btn-primary" style="width:100%;justify-content:center;margin-top:8px;text-decoration:none">
        <span>Agendar Cita</span> <i class="fas fa-calendar-check"></i>
      </a>
    </div>
  </div>
</section>
