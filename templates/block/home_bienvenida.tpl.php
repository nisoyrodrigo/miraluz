<section id="subheader" class="bg-color-op-1">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="col-lg-12">
                <h1 class="split">Centro de Seguimiento</h1>
                <ul class="crumb wow fadeInUp">
                    <li><a href="<?=$url("cliente")?>">Inicio</a></li>
                    <li class="active">Centro de Seguimiento</li>
                </ul>   
            </div>
        </div>
    </div>
</section>

<section class="relative">
  <div class="container relative z-2">
    <div class="row g-4">

        <!-- CARD: Estatus de pedido -->
        <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0s">
          <div class="hover">
              <div class="relative overflow-hidden rounded-1">
                <a href="<?=$url("web/pedido")?>" class="d-block hover">
                  <div class="relative overflow-hidden rounded-1">
                    <img src="<?=$urlm("assets/images/services/1.webp");?>" class="w-100 hover-scale-1-2" alt="">
                    <div class="gradient-edge-bottom color h-90 op-8"></div>
                  </div>
              
                  <div class="p-4 relative bg-white rounded-1 mx-4 mt-min-100 z-2">
                    <div class="abs top-0 end-0 mt-min-30 me-4 circle bg-color w-60px h-60px">
                      <img src="<?=$urlm("assets/images/misc/up-right-arrow.webp");?>" class="w-60px p-20" alt="">
                    </div>
                    <h4>Ver estatus de tu pedido</h4>
                    <p class="mb-0">Consulta el avance de tus lentes ingresando tu número de ticket.</p>
                  </div>
                </a>
              </div>
          </div>
        </div>

        <!-- CARD: Solicitar factura -->
        <div class="col-lg-4 col-sm-6 wow fadeInUp" data-wow-delay="0.2s">
          <div class="hover">
              <div class="relative overflow-hidden rounded-1">
                <a href="<?=$url("web/facturacion")?>" class="d-block hover">
                  <div class="relative overflow-hidden rounded-1">
                    <img src="<?=$urlm("assets/images/services/2.webp");?>" class="w-100 hover-scale-1-2" alt="">
                    <div class="gradient-edge-bottom color h-90 op-8"></div>
                  </div>
              
                  <div class="p-4 relative bg-white rounded-1 mx-4 mt-min-100 z-2">
                    <div class="abs top-0 end-0 mt-min-30 me-4 circle bg-color w-60px h-60px">
                      <img src="<?=$urlm("assets/images/misc/up-right-arrow.webp");?>" class="w-60px p-20" alt="">
                    </div>
                    <h4>Solicitar factura</h4>
                    <p class="mb-0">Solicita tu factura fácilmente con tu ticket o número de venta.</p>
                  </div>
                </a>
              </div>
          </div>
        </div>

    </div>
  </div>
</section>

<!-- BANNER ABAJO, YA TRADUCIDO -->
<section class="bg-color relative text-light pt-50 pb-50">
  <div class="container relative z-2">
    <div class="row g-4 align-items-center">
      <div class="col-md-9">
        <h3 class="mb-0 fs-32 split">Agenda tu estudio de la vista y cuida tu salud visual</h3>
      </div>
      <div class="col-lg-3 text-lg-end">
        <a class="btn-main btn-line fx-slide" href="book-your-visit.html">
          <span>Agendar cita</span>
        </a>
      </div>
    </div>
  </div>
</section>
