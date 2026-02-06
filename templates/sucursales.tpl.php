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
        <div class="map-placeholder">
          <i class="fas fa-map-marked-alt"></i>
          <p>Mapa interactivo de sucursales</p>
          <p style="font-size:0.78rem;margin-top:8px;color:var(--ml-green-primary)">Se integrará con Google Maps</p>
        </div>
      </div>

      <!-- Lista de sucursales -->
      <div class="ml-branch-list">
        <div class="ml-branch-card active ml-reveal">
          <h3><i class="fas fa-store" style="color:var(--ml-green-primary);margin-right:8px;font-size:0.9rem"></i> Sucursal Centro</h3>
          <div class="branch-details">
            <div class="detail"><i class="fas fa-map-pin"></i> Av. Juárez #234, Col. Centro</div>
            <div class="detail"><i class="fas fa-clock"></i> Lun-Sáb: 9:00 - 20:00</div>
            <div class="detail"><i class="fas fa-phone"></i> (555) 123-4567</div>
          </div>
          <div class="branch-status"><span class="dot"></span> Abierto ahora</div>
        </div>

        <div class="ml-branch-card ml-reveal ml-reveal-delay-1">
          <h3><i class="fas fa-store" style="color:var(--ml-green-primary);margin-right:8px;font-size:0.9rem"></i> Sucursal Plaza Norte</h3>
          <div class="branch-details">
            <div class="detail"><i class="fas fa-map-pin"></i> Plaza Norte, Local 15-B</div>
            <div class="detail"><i class="fas fa-clock"></i> Lun-Dom: 10:00 - 21:00</div>
            <div class="detail"><i class="fas fa-phone"></i> (555) 234-5678</div>
          </div>
          <div class="branch-status"><span class="dot"></span> Abierto ahora</div>
        </div>

        <div class="ml-branch-card ml-reveal ml-reveal-delay-2">
          <h3><i class="fas fa-store" style="color:var(--ml-green-primary);margin-right:8px;font-size:0.9rem"></i> Sucursal Valle</h3>
          <div class="branch-details">
            <div class="detail"><i class="fas fa-map-pin"></i> Blvd. del Valle #890</div>
            <div class="detail"><i class="fas fa-clock"></i> Lun-Sáb: 9:00 - 19:00</div>
            <div class="detail"><i class="fas fa-phone"></i> (555) 345-6789</div>
          </div>
          <div class="branch-status" style="color:var(--ml-text-muted)"><span class="dot" style="background:var(--ml-text-muted);animation:none"></span> Cerrado</div>
        </div>
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
