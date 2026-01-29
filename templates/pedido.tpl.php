<section id="subheader" class="bg-color-op-1">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="col-lg-12">
                <h1 class="split">Consulta tu Pedido</h1>
                <ul class="crumb wow fadeInUp">
                    <li><a href="<?=$url("seguimiento")?>">Centro de Seguimiento</a></li>
                    <li class="active">Estatus de Pedido</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="relative pt-5 pb-5">
  <div class="container relative z-2">
    <div class="row justify-content-center">
      
      <div class="col-lg-6 col-md-8">
        
        <div class="p-4 rounded-1 bg-white shadow-sm">
          
          <h3 class="mb-3 text-center">Ver estatus de tu pedido</h3>
          <p class="text-center mb-4">
            Ingresa tu <strong>número de venta</strong> y tu <strong>clave</strong>.
          </p>

          <!-- FORM CORRECTO -->
          <form id="formPedido">

            <div class="mb-3">
              <label class="form-label">Número de nota</label>
              <input type="text" name="venta" class="form-control" required>
            </div>

            <div class="mb-4">
              <label class="form-label">Clave</label>
              <input type="password" name="clave" class="form-control" required>
            </div>

            <div class="text-center">
              <button type="submit" class="btn-main" id="btnConsultar">
                Consultar estatus
              </button>
            </div>

          </form>

          <!-- Contenedor del resultado -->
          <div id="resultado" class="mt-4" style="display:none;"></div>

        </div>

      </div>

    </div>
  </div>
</section>

<script>
$(document).ready(function() {

  $("#formPedido").submit(function(e){
      e.preventDefault(); 

      let form = $(this);
      let btn = $("#btnConsultar");
      let resultado = $("#resultado");

      btn.prop("disabled", true).text("Consultando...");
      resultado.hide().html("");

      $.ajax({
          url: "<?=$url("web/default/checkpedido")?>",
          type: "POST",
          data: form.serialize(),
          dataType: "json",
          success: function(res){

              btn.prop("disabled", false).text("Consultar estatus");
              console.log("Respuesta:", res);

              // Si no trae data o id, mostramos error
              if (!res.data || !res.data.id) {
                  resultado
                    .html(`
                      <div class="alert alert-danger">
                          No se encontró el pedido. Verifica tu número de venta y clave.
                      </div>
                    `)
                    .show();
                  return;
              }

              // ⚙️ Mapa de estatus (los IDs son los de ec_venta_estatus)
              const statusMap = {
                  1: {
                      title: "Pendiente",
                      icon: "bi-hourglass-split",
                      text: "Tu pedido fue registrado y está por iniciar proceso."
                  },
                  7: {
                      title: "Apartado",
                      icon: "bi-receipt-cutoff",
                      text: "Tu venta está apartada. En cuanto se confirme el anticipo pasará a producción."
                  },
                  2: {
                      title: "Laboratorio",
                      icon: "bi-gear",
                      text: "Tus lentes están siendo procesados en el laboratorio."
                  },
                  3: {
                      title: "Sucursal",
                      icon: "bi-shop",
                      text: "Tus lentes ya están en sucursal. Puedes pasar a recogerlos cuando te lo indiquen."
                  },
                  4: {
                      title: "Entregado",
                      icon: "bi-check-circle-fill text-success",
                      text: "Tu pedido fue entregado. Gracias por tu confianza en Óptica Miraluz."
                  },
                  5: {
                      title: "Garantía",
                      icon: "bi-wrench-adjustable-circle",
                      text: "Tu producto está en proceso de garantía. Un asesor te contactará."
                  },
                  6: {
                      title: "Cancelada",
                      icon: "bi-x-circle-fill text-danger",
                      text: "La venta fue cancelada. Si tienes dudas, contáctanos."
                  }
              };

              // 👀 Ajusta aquí el nombre del campo según lo que regrese tu JSON
              // por ahora asumo que tu tabla 'venta' tiene un campo 'estatus'
              const estatusId = parseInt(res.data.estatus, 10) || 0;
              const status    = statusMap[estatusId] || {
                  title: "Estatus no disponible",
                  icon: "bi-question-circle",
                  text: "No fue posible determinar el estatus actual de tu pedido."
              };

              // Campos opcionales: ajusta a los nombres reales que regrese Venta
              const folio        = res.data.folio || "";
              const cliente      = res.data.cliente_obj.nombre || "";
              const fechaVenta   = res.data.fecha_venta || "";
              const fechaUpdate  = res.data.modified || "";

              // 🧱 Card bonito con icono + info
              resultado.html(`
                  <div class="card p-3 shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                      <i class="bi ${status.icon}" style="font-size:40px;"></i>
                      <div class="ms-3">
                        <h4 class="mb-1">${status.title}</h4>
                        <p class="text-muted mb-0">${status.text}</p>
                      </div>
                    </div>

                    <hr class="my-3" />

                    <ul class="list-unstyled mb-0">
                      ${folio       ? `<li><strong>Venta:</strong> ${folio}</li>` : ""}
                      ${cliente     ? `<li><strong>Cliente:</strong> ${cliente}</li>` : ""}
                      ${fechaVenta  ? `<li><strong>Fecha de venta:</strong> ${fechaVenta}</li>` : ""}
                      ${fechaUpdate ? `<li><strong>Última actualización:</strong> ${fechaUpdate}</li>` : ""}
                    </ul>
                  </div>
              `).show();
          },

          error: function(){
              btn.prop("disabled", false).text("Consultar estatus");
              resultado
                .html(`
                  <div class="alert alert-danger">
                      Error al conectar con el servidor. Intenta de nuevo en unos minutos.
                  </div>
                `)
                .show();
          }
      });
  });

});
</script>


