<nav class="ml-nav" id="mlNavbar">
  <div class="ml-nav-inner">
    <!-- Logo -->
    <a href="<?=$url("")?>" class="ml-nav-logo">
      <img src="<?=$url("images/logo-optica.png");?>" alt="Ópticas Miraluz">
      <span>Ópticas <span class="accent">Miraluz</span></span>
    </a>

    <!-- Menu Links -->
    <!-- Links originales (comentados):
      <li><a href="<?=$url("catalogo")?>">Catálogo</a></li>
      <li><a href="<?=$url("productos")?>">Productos</a></li>
      <li><a href="<?=$url("sucursales")?>">Sucursales</a></li>
      <li><a href="<?=$url("promos")?>">Promociones</a></li>
      <li><a href="<?=$url("blog")?>">Blog</a></li>
      <li><a href="<?=$url("citas")?>">Agendar Cita</a></li>
    -->
    <ul class="ml-nav-links" id="mlNavLinks">
      <li><a href="#">Catálogo</a></li>
      <li><a href="#">Productos</a></li>
      <li><a href="#">Sucursales</a></li>
      <li><a href="#">Promociones</a></li>
      <li><a href="#">Blog</a></li>
      <li><a href="<?=$url("seguimiento")?>">Mi Pedido</a></li>
    </ul>

    <!-- CTA Button -->
    <!-- Original: <a href="<?=$url("citas")?>" class="ml-nav-cta"> -->
    <a href="#" class="ml-nav-cta">
      <i class="fas fa-calendar-check"></i> Agendar Cita
    </a>

    <!-- Hamburger Menu (Mobile) -->
    <button class="ml-hamburger" id="mlHamburger" aria-label="Menú">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</nav>
