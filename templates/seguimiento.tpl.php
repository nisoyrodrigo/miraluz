<!-- SUBHEADER -->
<section class="ml-section" style="padding: 140px 40px 60px; background: linear-gradient(180deg, var(--ml-dark) 0%, var(--ml-dark-card) 100%);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <div class="ml-section-tag">Centro de Atención</div>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 16px;">Centro de Seguimiento</h1>
    <p style="color: var(--ml-text-secondary); max-width: 500px; margin: 0 auto;">
      Consulta el estado de tu pedido o solicita tu factura de forma rápida y sencilla.
    </p>
  </div>
</section>

<!-- CARDS DE OPCIONES -->
<section class="ml-section" style="padding: 80px 40px;">
  <div style="max-width: 900px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">

      <!-- Card: Ver estatus de pedido -->
      <a href="<?=$url("pedido")?>" class="ml-service-card ml-reveal" style="text-decoration: none;">
        <div class="ml-service-icon">
          <i class="fas fa-search"></i>
        </div>
        <h3>Ver estatus de tu pedido</h3>
        <p>Consulta el avance de tus lentes ingresando tu número de nota y clave.</p>
        <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
      </a>

      <!-- Card: Solicitar factura -->
      <a href="<?=$url("facturacion")?>" class="ml-service-card ml-reveal ml-reveal-delay-1" style="text-decoration: none;">
        <div class="ml-service-icon">
          <i class="fas fa-file-invoice"></i>
        </div>
        <h3>Solicitar factura</h3>
        <p>Solicita tu factura fácilmente con tu número de nota y datos fiscales.</p>
        <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
      </a>

    </div>
  </div>
</section>

<!-- BANNER CTA -->
<section style="background: var(--ml-green-primary); padding: 60px 40px;">
  <div style="max-width: 1000px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
    <h3 style="font-size: 1.5rem; color: var(--ml-dark); margin: 0; font-family: 'DM Sans', sans-serif;">
      Agenda tu estudio de la vista y cuida tu salud visual
    </h3>
    <a href="<?=$url("citas")?>" class="ml-btn-secondary" style="border-color: var(--ml-dark); color: var(--ml-dark);">
      <span>Agendar cita</span> <i class="fas fa-calendar-check"></i>
    </a>
  </div>
</section>
