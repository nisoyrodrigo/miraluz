<!-- HERO SLIDER -->
<section class="ml-hero-slider" id="ml-inicio">
  <!-- SLIDE 1 -->
  <div class="ml-slider-slide active" data-type="image">
    <div class="ml-slide-bg">
      <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=1280&q=70" alt="Armazones de diseñador">
    </div>
    <div class="ml-slide-overlay"></div>
    <div class="ml-slide-content">
      <div class="ml-slide-inner">
        <div class="ml-slide-tag"><span class="dot"></span> Nueva Colección 2026</div>
        <h1 class="ml-slide-title">Visión perfecta,<br><span class="highlight">estilo único</span></h1>
        <p class="ml-slide-desc">Descubre nuestra colección exclusiva de armazones de diseñador. Las mejores marcas internacionales en un solo lugar.</p>
        <div class="ml-slide-actions">
          <a href="<?=$url("productos")?>" class="ml-btn-primary"><span>Ver Colección</span> <i class="fas fa-arrow-right"></i></a>
          <a href="<?=$url("citas")?>" class="ml-btn-secondary"><i class="fas fa-eye"></i> Examen Visual</a>
        </div>
      </div>
    </div>
  </div>

  <!-- SLIDE 2 -->
  <div class="ml-slider-slide" data-type="image">
    <div class="ml-slide-bg">
      <img src="https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=1280&q=70" alt="Lentes de sol" loading="lazy">
    </div>
    <div class="ml-slide-overlay"></div>
    <div class="ml-slide-content">
      <div class="ml-slide-inner">
        <div class="ml-slide-tag"><span class="dot"></span> Promoción Especial</div>
        <h1 class="ml-slide-title"><span class="highlight">2x1</span> en armazones<br>seleccionados</h1>
        <p class="ml-slide-desc">Compra un armazón y llévate el segundo completamente gratis. Aplica en modelos seleccionados de las mejores marcas.</p>
        <div class="ml-slide-actions">
          <a href="<?=$url("promos")?>" class="ml-btn-primary"><span>Ver Promociones</span> <i class="fas fa-tags"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- SLIDE 3 -->
  <div class="ml-slider-slide" data-type="image">
    <div class="ml-slide-bg">
      <img src="https://images.unsplash.com/photo-1577803645773-f96470509666?w=1280&q=70" alt="Examen visual" loading="lazy">
    </div>
    <div class="ml-slide-overlay"></div>
    <div class="ml-slide-content">
      <div class="ml-slide-inner">
        <div class="ml-slide-tag"><span class="dot"></span> Salud Visual</div>
        <h1 class="ml-slide-title">Tu visión merece<br>la <span class="highlight">mejor atención</span></h1>
        <p class="ml-slide-desc">Examen visual completo con equipo de última generación. Nuestros optometristas certificados te esperan.</p>
        <div class="ml-slide-actions">
          <a href="<?=$url("citas")?>" class="ml-btn-primary"><span>Agendar Cita</span> <i class="fas fa-calendar-check"></i></a>
          <a href="<?=$url("sucursales")?>" class="ml-btn-secondary"><i class="fas fa-map-pin"></i> Sucursales</a>
        </div>
      </div>
    </div>
  </div>

  <!-- SLIDE 4 -->
  <div class="ml-slider-slide" data-type="image">
    <div class="ml-slide-bg">
      <img src="https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=1280&q=70" alt="Lentes de contacto" loading="lazy">
    </div>
    <div class="ml-slide-overlay"></div>
    <div class="ml-slide-content">
      <div class="ml-slide-inner">
        <div class="ml-slide-tag"><span class="dot"></span> Lentes de Contacto</div>
        <h1 class="ml-slide-title">Libertad total<br>con <span class="highlight">lentes de contacto</span></h1>
        <p class="ml-slide-desc">Amplia variedad de lentes de contacto graduados, cosméticos y especializados. Asesoría experta incluida.</p>
        <div class="ml-slide-actions">
          <a href="<?=$url("productos")?>" class="ml-btn-primary"><span>Explorar</span> <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- SLIDE 5 -->
  <div class="ml-slider-slide" data-type="image">
    <div class="ml-slide-bg">
      <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1280&q=70" alt="Programa de lealtad" loading="lazy">
    </div>
    <div class="ml-slide-overlay"></div>
    <div class="ml-slide-content">
      <div class="ml-slide-inner">
        <div class="ml-slide-tag"><span class="dot"></span> Puntos Miraluz</div>
        <h1 class="ml-slide-title">Acumula puntos,<br><span class="highlight">gana premios</span></h1>
        <p class="ml-slide-desc">Únete a nuestro programa de lealtad. Cada compra te acerca a increíbles recompensas y descuentos exclusivos.</p>
        <div class="ml-slide-actions">
          <a href="#ml-lealtad" class="ml-btn-primary"><span>Conocer Más</span> <i class="fas fa-gift"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Arrows -->
  <div class="ml-slider-arrows">
    <button class="ml-slider-arrow" id="mlSliderPrev"><i class="fas fa-chevron-left"></i></button>
    <button class="ml-slider-arrow" id="mlSliderNext"><i class="fas fa-chevron-right"></i></button>
  </div>

  <!-- Dots -->
  <div class="ml-slider-controls">
    <div class="ml-slider-dots" id="mlSliderDots"></div>
  </div>

  <!-- Counter -->
  <div class="ml-slider-counter">
    <span class="current" id="mlSliderCurrent">01</span>
    <span class="divider"></span>
    <span id="mlSliderTotal">05</span>
  </div>

  <!-- Progress -->
  <div class="ml-slider-progress">
    <div class="ml-slider-progress-bar" id="mlSliderProgress"></div>
  </div>
</section>

<!-- STATS BAR -->
<div class="ml-stats-bar">
  <div class="ml-stats-bar-inner">
    <div class="ml-stat"><div class="ml-stat-number" data-count="15">0</div><div class="ml-stat-label">Años de experiencia</div></div>
    <div class="ml-stat"><div class="ml-stat-number" data-count="2">0</div><div class="ml-stat-label">Sucursales</div></div>
    <div class="ml-stat"><div class="ml-stat-number" data-count="10000">0</div><div class="ml-stat-label">Clientes satisfechos</div></div>
    <div class="ml-stat"><div class="ml-stat-number" data-count="50">0</div><div class="ml-stat-label">Marcas premium</div></div>
  </div>
</div>

<!-- MARQUEE -->
<div class="ml-marquee">
  <div class="ml-marquee-track" id="mlMarqueeTrack">
    <span><i class="fas fa-check-circle"></i> Lentes graduados en 24h</span>
    <span><i class="fas fa-check-circle"></i> Envío gratis a toda la república</span>
    <span><i class="fas fa-check-circle"></i> Garantía de 1 año</span>
    <span><i class="fas fa-check-circle"></i> Marcas premium</span>
    <span><i class="fas fa-check-circle"></i> Examen visual gratuito</span>
    <span><i class="fas fa-check-circle"></i> Programa de puntos Miraluz</span>
    <span><i class="fas fa-check-circle"></i> Aceptamos todas las tarjetas</span>
    <span><i class="fas fa-check-circle"></i> Lentes de contacto especializados</span>
    <span><i class="fas fa-check-circle"></i> Lentes graduados en 24h</span>
    <span><i class="fas fa-check-circle"></i> Envío gratis a toda la república</span>
    <span><i class="fas fa-check-circle"></i> Garantía de 1 año</span>
    <span><i class="fas fa-check-circle"></i> Marcas premium</span>
    <span><i class="fas fa-check-circle"></i> Examen visual gratuito</span>
    <span><i class="fas fa-check-circle"></i> Programa de puntos Miraluz</span>
    <span><i class="fas fa-check-circle"></i> Aceptamos todas las tarjetas</span>
    <span><i class="fas fa-check-circle"></i> Lentes de contacto especializados</span>
  </div>
</div>

<!-- CATÁLOGO / SERVICIOS -->
<section class="ml-section" id="ml-catalogo">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Nuestros Servicios</div>
    <h2>Todo para tu salud visual</h2>
    <p>Ofrecemos una experiencia integral en cuidado de la visión con los más altos estándares de calidad</p>
  </div>

  <div class="ml-services-grid">
    <a href="<?=$url("productos")?>" class="ml-service-card ml-reveal" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-glasses"></i></div>
      <h3>Armazones de Diseñador</h3>
      <p>Colección curada de las mejores marcas internacionales. Encuentra el estilo perfecto para tu personalidad.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>

    <a href="<?=$url("citas")?>" class="ml-service-card ml-reveal ml-reveal-delay-1" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-eye"></i></div>
      <h3>Examen Visual</h3>
      <p>Evaluación completa con equipos de última generación. Nuestros optometristas están certificados y actualizados.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>

    <a href="<?=$url("productos")?>" class="ml-service-card ml-reveal ml-reveal-delay-2" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-circle-dot"></i></div>
      <h3>Lentes de Contacto</h3>
      <p>Amplia variedad de lentes de contacto graduados, cosméticos y especializados para cada necesidad.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>

    <a href="<?=$url("productos")?>" class="ml-service-card ml-reveal ml-reveal-delay-1" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-sun"></i></div>
      <h3>Lentes Solares</h3>
      <p>Protege tus ojos con estilo. Lentes solares graduados y no graduados con protección UV certificada.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>

    <a href="<?=$url("productos")?>" class="ml-service-card ml-reveal ml-reveal-delay-2" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-children"></i></div>
      <h3>Óptica Infantil</h3>
      <p>Línea especializada para los más pequeños. Armazones resistentes, coloridos y con diseños divertidos.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>

    <a href="<?=$url("sucursales")?>" class="ml-service-card ml-reveal ml-reveal-delay-3" style="text-decoration:none">
      <div class="ml-service-icon"><i class="fas fa-wrench"></i></div>
      <h3>Reparaciones y Ajustes</h3>
      <p>Servicio técnico experto. Reparamos y ajustamos tus lentes para que siempre estén como nuevos.</p>
      <div class="card-arrow"><i class="fas fa-arrow-right"></i></div>
    </a>
  </div>
</section>

<!-- PRODUCTOS DESTACADOS -->
<section class="ml-section ml-section-alt" id="ml-productos">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Productos Destacados</div>
    <h2>Lo más nuevo y popular</h2>
    <p>Descubre las últimas tendencias en armazones y lentes</p>
  </div>

  <div class="ml-products-showcase">
    <div class="ml-products-tabs ml-reveal">
      <button class="ml-tab-btn active">Todos</button>
      <button class="ml-tab-btn">Armazones</button>
      <button class="ml-tab-btn">Solares</button>
      <button class="ml-tab-btn">Lentes de Contacto</button>
    </div>

    <div class="ml-products-grid">
      <!-- Producto 1 -->
      <div class="ml-product-card ml-reveal">
        <div class="ml-product-img">
          <img src="https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=400&q=70" loading="lazy" alt="Ray-Ban Wayfarer">
          <span class="badge">Nuevo</span>
          <button class="wishlist"><i class="far fa-heart"></i></button>
        </div>
        <div class="ml-product-info">
          <div class="ml-product-brand">Ray-Ban</div>
          <div class="ml-product-name">Wayfarer Classic RB2140</div>
          <div class="ml-product-price">
            <span class="current">$3,490</span>
            <span class="old">$4,200</span>
          </div>
          <div class="ml-product-actions">
            <button class="add-cart"><i class="fas fa-shopping-bag"></i> Apartar</button>
            <button class="quick-view"><i class="fas fa-eye"></i></button>
          </div>
        </div>
      </div>

      <!-- Producto 2 -->
      <div class="ml-product-card ml-reveal ml-reveal-delay-1">
        <div class="ml-product-img">
          <img src="https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400&q=70" loading="lazy" alt="Oakley">
          <button class="wishlist"><i class="far fa-heart"></i></button>
        </div>
        <div class="ml-product-info">
          <div class="ml-product-brand">Oakley</div>
          <div class="ml-product-name">Holbrook OO9102</div>
          <div class="ml-product-price">
            <span class="current">$2,890</span>
          </div>
          <div class="ml-product-actions">
            <button class="add-cart"><i class="fas fa-shopping-bag"></i> Apartar</button>
            <button class="quick-view"><i class="fas fa-eye"></i></button>
          </div>
        </div>
      </div>

      <!-- Producto 3 -->
      <div class="ml-product-card ml-reveal ml-reveal-delay-2">
        <div class="ml-product-img">
          <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400&q=70" loading="lazy" alt="Versace">
          <span class="badge">-20%</span>
          <button class="wishlist"><i class="far fa-heart"></i></button>
        </div>
        <div class="ml-product-info">
          <div class="ml-product-brand">Versace</div>
          <div class="ml-product-name">VE4361 Cat Eye</div>
          <div class="ml-product-price">
            <span class="current">$4,990</span>
            <span class="old">$6,200</span>
          </div>
          <div class="ml-product-actions">
            <button class="add-cart"><i class="fas fa-shopping-bag"></i> Apartar</button>
            <button class="quick-view"><i class="fas fa-eye"></i></button>
          </div>
        </div>
      </div>

      <!-- Producto 4 -->
      <div class="ml-product-card ml-reveal ml-reveal-delay-3">
        <div class="ml-product-img">
          <img src="https://images.unsplash.com/photo-1591076482161-42ce6da69f67?w=400&q=70" loading="lazy" alt="Acuvue">
          <span class="badge">Popular</span>
          <button class="wishlist"><i class="far fa-heart"></i></button>
        </div>
        <div class="ml-product-info">
          <div class="ml-product-brand">Acuvue</div>
          <div class="ml-product-name">Oasys 1-Day (30 pzas)</div>
          <div class="ml-product-price">
            <span class="current">$890</span>
          </div>
          <div class="ml-product-actions">
            <button class="add-cart"><i class="fas fa-shopping-bag"></i> Apartar</button>
            <button class="quick-view"><i class="fas fa-eye"></i></button>
          </div>
        </div>
      </div>
    </div>

    <div style="text-align: center; margin-top: 48px;">
      <a href="<?=$url("productos")?>" class="ml-btn-primary"><span>Ver Todos los Productos</span> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- SUCURSALES -->
<?php $sucursales = Sucursal::model()->findAll("WHERE id IN (2, 4) AND estatus = 1"); ?>
<section class="ml-section" id="ml-sucursales">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Nuestras Sucursales</div>
    <h2>Estamos cerca de ti</h2>
    <p>Visítanos en cualquiera de nuestras sucursales y vive la experiencia Miraluz</p>
  </div>

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
</section>

<!-- PROMOCIONES -->
<section class="ml-section ml-section-promos" id="ml-promos">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Ofertas Especiales</div>
    <h2>Promociones del mes</h2>
    <p>Aprovecha nuestras promociones exclusivas y ahorra en tu compra</p>
  </div>

  <div class="ml-promos-slider ml-reveal">
    <div class="ml-promos-track" id="mlPromosTrack">
      <!-- Promo 1 -->
      <div class="ml-promo-card">
        <div class="promo-glow"></div>
        <div class="promo-discount">2x1</div>
        <h3>Armazones Seleccionados</h3>
        <p>Compra un armazón y llévate el segundo gratis en modelos seleccionados.</p>
        <div class="promo-validity"><i class="fas fa-calendar"></i> Válido hasta 28 Feb 2026</div>
      </div>

      <!-- Promo 2 -->
      <div class="ml-promo-card">
        <div class="promo-glow"></div>
        <div class="promo-discount">30%</div>
        <h3>Lentes Solares Premium</h3>
        <p>Descuento en toda la línea de lentes solares Ray-Ban, Oakley y Prada.</p>
        <div class="promo-validity"><i class="fas fa-calendar"></i> Válido hasta 15 Mar 2026</div>
      </div>

      <!-- Promo 3 -->
      <div class="ml-promo-card">
        <div class="promo-glow"></div>
        <div class="promo-discount">$499</div>
        <h3>Examen Visual Completo</h3>
        <p>Evaluación completa con topografía corneal incluida. Precio especial por tiempo limitado.</p>
        <div class="promo-validity"><i class="fas fa-calendar"></i> Válido todo febrero</div>
      </div>

      <!-- Promo 4 -->
      <div class="ml-promo-card">
        <div class="promo-glow"></div>
        <div class="promo-discount">15%</div>
        <h3>Lentes de Contacto</h3>
        <p>En la compra de 3 cajas o más de lentes de contacto Acuvue o Air Optix.</p>
        <div class="promo-validity"><i class="fas fa-calendar"></i> Válido hasta 10 Mar 2026</div>
      </div>
    </div>

    <div class="ml-promo-nav">
      <button id="promosPrev" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
      <button id="promosNext" aria-label="Siguiente"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>
</section>

<!-- BLOG -->
<section class="ml-section" id="ml-blog">
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Blog de Salud Visual</div>
    <h2>Artículos y consejos</h2>
    <p>Mantente informado sobre el cuidado de tu visión</p>
  </div>

  <div class="ml-blog-grid">
    <!-- Artículo Principal -->
    <div class="ml-blog-card ml-blog-featured ml-reveal">
      <div class="ml-blog-img">
        <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=800&q=70" loading="lazy" alt="Salud visual">
        <span class="blog-category">Salud Visual</span>
      </div>
      <div class="ml-blog-content">
        <div class="ml-blog-meta">
          <span><i class="far fa-calendar"></i> 2 Feb 2026</span>
          <span><i class="far fa-clock"></i> 5 min</span>
        </div>
        <h3>¿Cada cuánto debes revisar tu graduación? La guía completa</h3>
        <p>Descubre la frecuencia ideal para tus exámenes visuales según tu edad y condición. La prevención es la mejor herramienta para mantener una visión saludable.</p>
        <a href="<?=$url("blog")?>" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <!-- Artículo 2 -->
    <div class="ml-blog-card ml-reveal ml-reveal-delay-1">
      <div class="ml-blog-img">
        <img src="https://images.unsplash.com/photo-1574258495973-f010dfbb5371?w=400&q=70" loading="lazy" alt="Tendencias">
        <span class="blog-category">Tendencias</span>
      </div>
      <div class="ml-blog-content">
        <div class="ml-blog-meta">
          <span><i class="far fa-calendar"></i> 28 Ene 2026</span>
          <span><i class="far fa-clock"></i> 3 min</span>
        </div>
        <h3>Tendencias en armazones 2026</h3>
        <p>Los estilos que dominarán este año: geometría audaz y transparencias.</p>
        <a href="<?=$url("blog")?>" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>

    <!-- Artículo 3 -->
    <div class="ml-blog-card ml-reveal ml-reveal-delay-2">
      <div class="ml-blog-img">
        <img src="https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=400&q=70" loading="lazy" alt="Consejos">
        <span class="blog-category">Consejos</span>
      </div>
      <div class="ml-blog-content">
        <div class="ml-blog-meta">
          <span><i class="far fa-calendar"></i> 20 Ene 2026</span>
          <span><i class="far fa-clock"></i> 4 min</span>
        </div>
        <h3>Protege tus ojos de las pantallas</h3>
        <p>Tips y lentes especiales para reducir la fatiga visual digital.</p>
        <a href="<?=$url("blog")?>" class="read-more">Leer más <i class="fas fa-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- CITAS -->
<section class="ml-section ml-section-alt" id="ml-citas">
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
      <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&q=70" loading="lazy" alt="Examen visual" style="width:100%;border-radius:var(--ml-radius-lg);margin-top:24px;opacity:0.9">
    </div>

    <!-- Formulario -->
    <div class="ml-citas-form ml-reveal ml-reveal-delay-1">
      <h3>Llena tus datos</h3>
      <form id="formCitaHome">
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
            <?php foreach ($sucursales as $suc): ?>
            <option value="<?=$suc->id?>"><?=$suc->nombre?></option>
            <?php endforeach; ?>
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
        <a href="<?=$url("citas")?>" class="ml-btn-primary" style="width:100%;justify-content:center;margin-top:8px;text-decoration:none">
          <span>Agendar Cita</span> <i class="fas fa-calendar-check"></i>
        </a>
      </form>
    </div>
  </div>
</section>

<!-- PROGRAMA DE LEALTAD -->
<section class="ml-section" id="ml-lealtad">
  <div class="ml-lealtad-bg"></div>
  <div class="ml-section-header ml-reveal">
    <div class="ml-section-tag">Programa de Lealtad</div>
    <h2>Acumula puntos, <span class="shine">gana premios</span></h2>
    <p>Cada compra te acerca a increíbles recompensas y descuentos exclusivos</p>
  </div>

  <div class="ml-levels-grid">
    <div class="ml-level-card ml-reveal">
      <div class="ml-level-icon bronze"><i class="fas fa-medal"></i></div>
      <h3>Bronce</h3>
      <div class="points">0 - 999 puntos</div>
      <ul class="level-benefits">
        <li><i class="fas fa-check"></i> 5% de descuento en tu cumpleaños</li>
        <li><i class="fas fa-check"></i> Limpieza de lentes gratis</li>
        <li><i class="fas fa-check"></i> Acceso a promociones exclusivas</li>
      </ul>
    </div>

    <div class="ml-level-card featured ml-reveal ml-reveal-delay-1">
      <div class="ml-level-icon gold"><i class="fas fa-crown"></i></div>
      <h3>Oro</h3>
      <div class="points">1,000 - 4,999 puntos</div>
      <ul class="level-benefits">
        <li><i class="fas fa-check"></i> 10% de descuento en tu cumpleaños</li>
        <li><i class="fas fa-check"></i> Examen visual gratis anual</li>
        <li><i class="fas fa-check"></i> Envío gratis en pedidos</li>
        <li><i class="fas fa-check"></i> Ajustes ilimitados gratis</li>
      </ul>
    </div>

    <div class="ml-level-card ml-reveal ml-reveal-delay-2">
      <div class="ml-level-icon platinum"><i class="fas fa-gem"></i></div>
      <h3>Platino</h3>
      <div class="points">5,000+ puntos</div>
      <ul class="level-benefits">
        <li><i class="fas fa-check"></i> 15% de descuento siempre</li>
        <li><i class="fas fa-check"></i> Acceso VIP a nuevas colecciones</li>
        <li><i class="fas fa-check"></i> Garantía extendida 2 años</li>
        <li><i class="fas fa-check"></i> Reparaciones express gratis</li>
      </ul>
    </div>
  </div>
</section>

<!-- MI CUENTA PREVIEW -->
<section class="ml-section ml-section-alt" id="ml-cuenta">
  <div class="ml-account-preview">
    <div class="ml-account-mockup ml-reveal">
      <div class="mockup-bar">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
      </div>
      <div class="mockup-body">
        <div class="mockup-sidebar">
          <span class="active">Mi Cuenta</span>
          <span>Mis Pedidos</span>
          <span>Puntos</span>
          <span>Recetas</span>
        </div>
        <div class="mockup-content">
          <div class="mockup-row">
            <span class="label">Pedido #4521</span>
            <span class="status green">En sucursal</span>
          </div>
          <div class="mockup-row">
            <span class="label">Pedido #4489</span>
            <span class="status yellow">En laboratorio</span>
          </div>
          <div class="mockup-row">
            <span class="label">Puntos acumulados</span>
            <span class="value" style="color:var(--ml-green-primary)">2,450 pts</span>
          </div>
          <div class="mockup-row">
            <span class="label">Nivel actual</span>
            <span class="status blue">Oro</span>
          </div>
        </div>
      </div>
    </div>

    <div class="ml-account-features ml-reveal ml-reveal-delay-1">
      <h3>Tu cuenta, todo en un solo lugar</h3>
      <p>Accede a tu historial de compras, recetas, puntos acumulados y más. Todo desde cualquier dispositivo.</p>
      <div class="features-list">
        <div class="feature-item">
          <div class="icon"><i class="fas fa-truck"></i></div>
          <div class="text">
            <h4>Seguimiento en tiempo real</h4>
            <p>Rastrea tus pedidos desde el laboratorio hasta la sucursal</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="icon"><i class="fas fa-file-medical"></i></div>
          <div class="text">
            <h4>Historial de recetas</h4>
            <p>Accede a todas tus recetas y graduaciones anteriores</p>
          </div>
        </div>
        <div class="feature-item">
          <div class="icon"><i class="fas fa-gift"></i></div>
          <div class="text">
            <h4>Puntos y recompensas</h4>
            <p>Consulta tus puntos y canjéalos por increíbles premios</p>
          </div>
        </div>
      </div>
      <a href="<?=$url("seguimiento")?>" class="ml-btn-primary"><span>Consultar Mi Pedido</span> <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- CTA FINAL -->
<div class="ml-cta-section">
  <div class="cta-glow"></div>
  <h2 class="ml-reveal">¿Listo para ver el mundo<br>con nuevos ojos?</h2>
  <p class="ml-reveal ml-reveal-delay-1">Agenda tu cita hoy y descubre por qué miles de personas confían en Ópticas Miraluz para el cuidado de su visión.</p>
  <div class="ml-cta-actions ml-reveal ml-reveal-delay-2">
    <a href="<?=$url("citas")?>" class="ml-btn-primary"><span>Agendar Cita</span> <i class="fas fa-calendar-check"></i></a>
    <a href="<?=$url("sucursales")?>" class="ml-btn-secondary"><i class="fas fa-map-pin"></i> Visitar Sucursal</a>
  </div>
</div>
