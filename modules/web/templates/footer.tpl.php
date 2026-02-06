<footer class="ml-footer">
  <div class="ml-footer-content">
    <!-- Brand -->
    <div class="ml-footer-brand">
      <a href="<?=$url("")?>" class="ml-nav-logo">
        <img src="<?=$url("images/logo-optica.png");?>" alt="Ópticas Miraluz" style="height:36px">
        <span>Ópticas <span class="accent">Miraluz</span></span>
      </a>
      <p>Tu salud visual es nuestra prioridad. Más de 15 años cuidando la visión de familias mexicanas con los más altos estándares de calidad y servicio.</p>
      <div class="ml-footer-social">
        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      </div>
    </div>

    <!-- Navegación -->
    <div class="ml-footer-col">
      <h4>Navegación</h4>
      <ul>
        <li><a href="<?=$url("catalogo")?>">Catálogo</a></li>
        <li><a href="<?=$url("productos")?>">Productos</a></li>
        <li><a href="<?=$url("sucursales")?>">Sucursales</a></li>
        <li><a href="<?=$url("promos")?>">Promociones</a></li>
        <li><a href="<?=$url("blog")?>">Blog</a></li>
      </ul>
    </div>

    <!-- Servicios -->
    <div class="ml-footer-col">
      <h4>Servicios</h4>
      <ul>
        <li><a href="<?=$url("citas")?>">Examen Visual</a></li>
        <li><a href="<?=$url("productos")?>">Lentes Graduados</a></li>
        <li><a href="<?=$url("productos")?>">Lentes de Contacto</a></li>
        <li><a href="<?=$url("seguimiento")?>">Mi Pedido</a></li>
        <li><a href="<?=$url("facturacion")?>">Facturación</a></li>
      </ul>
    </div>

    <!-- Legal -->
    <div class="ml-footer-col">
      <h4>Legal</h4>
      <ul>
        <li><a href="#">Aviso de Privacidad</a></li>
        <li><a href="#">Términos y Condiciones</a></li>
        <li><a href="#">Política de Garantía</a></li>
        <li><a href="#">Política de Devoluciones</a></li>
      </ul>
    </div>
  </div>

  <!-- Footer Bottom -->
  <div class="ml-footer-bottom">
    <span>© <?=date('Y');?> Ópticas Miraluz. Todos los derechos reservados.</span>
    <span>Hecho con <i class="fas fa-heart" style="color:var(--ml-green-primary)"></i> en México</span>
  </div>
</footer>

<!-- WhatsApp Float Button -->
<button class="ml-whatsapp-float" onclick="window.open('https://wa.me/5551234567','_blank')" aria-label="WhatsApp">
  <i class="fab fa-whatsapp"></i>
</button>
