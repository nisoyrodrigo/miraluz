<?php $sucursales = Sucursal::model()->findAll("WHERE id IN (2, 4) AND estatus = 1"); ?>
<!-- SUBHEADER -->
<section class="ml-section" style="padding: 140px 40px 60px; background: linear-gradient(180deg, var(--ml-dark) 0%, var(--ml-dark-card) 100%);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <div class="ml-section-tag">Nuestras Sucursales</div>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 16px;">Estamos Cerca de Ti</h1>
    <p style="color: var(--ml-text-secondary); max-width: 600px; margin: 0 auto;">
      Visítanos en cualquiera de nuestras sucursales y vive la experiencia Miraluz.
    </p>
  </div>
</section>

<!-- SUCURSALES -->
<section class="ml-section" style="padding: 80px 40px;">
  <div style="max-width: 1200px; margin: 0 auto;">
    <div class="ml-branches-container">
      <!-- Mapa -->
      <div class="ml-branch-map ml-reveal">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3762.6610993873!2d-99.16869032394938!3d19.427023981861!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85d1ff35f5bd1563%3A0x6c366f0e2de02ff7!2zQ2l1ZGFkIGRlIE3DqXhpY28!5e0!3m2!1ses!2smx!4v1707000000000!5m2!1ses!2smx"
          width="100%"
          height="100%"
          style="border:0;border-radius:var(--ml-radius-lg);"
          allowfullscreen=""
          loading="lazy">
        </iframe>
      </div>

      <!-- Lista de sucursales -->
      <div class="ml-branch-list">
        <?php foreach ($sucursales as $i => $suc): ?>
        <div class="ml-branch-card<?php if ($i == 0) echo ' active'; ?> ml-reveal<?php if ($i > 0) echo ' ml-reveal-delay-'.$i; ?>">
          <h3><i class="fas fa-store" style="color:var(--ml-green-primary);margin-right:8px;font-size:0.9rem"></i> <?=$suc->nombre?></h3>
          <div class="branch-details">
            <div class="detail"><i class="fas fa-map-pin"></i> <?=$suc->direccion?></div>
            <div class="detail"><i class="fas fa-clock"></i> <?=$suc->horario?></div>
            <div class="detail"><i class="fas fa-phone"></i> <?=$suc->telefono?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section style="background: var(--ml-green-primary); padding: 60px 40px;">
  <div style="max-width: 1000px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
    <h3 style="font-size: 1.5rem; color: var(--ml-dark); margin: 0; font-family: 'DM Sans', sans-serif;">
      Agenda tu cita en la sucursal más cercana
    </h3>
    <a href="<?=$url("citas")?>" class="ml-btn-secondary" style="border-color: var(--ml-dark); color: var(--ml-dark);">
      <span>Agendar Cita</span> <i class="fas fa-calendar-check"></i>
    </a>
  </div>
</section>
