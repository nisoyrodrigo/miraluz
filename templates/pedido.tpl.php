<!-- SUBHEADER -->
<section class="ml-section" style="padding: 140px 40px 60px; background: linear-gradient(180deg, var(--ml-dark) 0%, var(--ml-dark-card) 100%);">
  <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
    <div class="ml-section-tag">Consulta tu Pedido</div>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 16px;">Estatus de tu Pedido</h1>
    <nav style="display: flex; justify-content: center; gap: 8px; color: var(--ml-text-muted); font-size: 0.9rem;">
      <a href="<?=$url("seguimiento")?>" style="color: var(--ml-green-primary); text-decoration: none;">Centro de Seguimiento</a>
      <span>/</span>
      <span>Estatus de Pedido</span>
    </nav>
  </div>
</section>

<!-- FORMULARIO -->
<section class="ml-section" style="padding: 80px 40px;">
  <div style="max-width: 500px; margin: 0 auto;">
    <div class="ml-citas-form">
      <h3 style="text-align: center; margin-bottom: 8px;">Ver estatus de tu pedido</h3>
      <p style="text-align: center; color: var(--ml-text-secondary); margin-bottom: 24px;">
        Ingresa tu <strong style="color: var(--ml-text-primary);">número de nota</strong> y tu <strong style="color: var(--ml-text-primary);">clave</strong>.
      </p>

      <form id="formPedido">
        <div class="ml-form-group">
          <label>Número de nota</label>
          <input type="text" name="venta" placeholder="Ej: 1234" required>
        </div>

        <div class="ml-form-group">
          <label>Clave</label>
          <input type="password" name="clave" placeholder="Tu clave de acceso" required>
        </div>

        <button type="submit" class="ml-btn-primary" id="btnConsultar" style="width: 100%; justify-content: center; margin-top: 8px;">
          <span>Consultar estatus</span> <i class="fas fa-search"></i>
        </button>
      </form>

      <!-- Resultado -->
      <div id="resultado" style="display: none; margin-top: 24px;"></div>
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

    btn.prop("disabled", true).find("span").text("Consultando...");
    resultado.hide().html("");

    $.ajax({
      url: "<?=$url("web/default/checkpedido")?>",
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: function(res){
        btn.prop("disabled", false).find("span").text("Consultar estatus");

        if (!res.data || !res.data.id) {
          resultado.html(`
            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #ef4444;">
              <i class="fas fa-exclamation-circle"></i> No se encontró el pedido. Verifica tu número de nota y clave.
            </div>
          `).show();
          return;
        }

        const statusMap = {
          1: { title: "Pendiente", icon: "fa-hourglass-half", color: "#f59e0b", text: "Tu pedido fue registrado y está por iniciar proceso." },
          7: { title: "Apartado", icon: "fa-receipt", color: "#8b5cf6", text: "Tu venta está apartada. En cuanto se confirme el anticipo pasará a producción." },
          2: { title: "En Laboratorio", icon: "fa-flask", color: "#3b82f6", text: "Tus lentes están siendo procesados en el laboratorio." },
          3: { title: "En Sucursal", icon: "fa-store", color: "#10b981", text: "Tus lentes ya están en sucursal. Puedes pasar a recogerlos." },
          4: { title: "Entregado", icon: "fa-check-circle", color: "#22c55e", text: "Tu pedido fue entregado. ¡Gracias por tu confianza!" },
          5: { title: "En Garantía", icon: "fa-wrench", color: "#f97316", text: "Tu producto está en proceso de garantía." },
          6: { title: "Cancelada", icon: "fa-times-circle", color: "#ef4444", text: "La venta fue cancelada. Contáctanos si tienes dudas." }
        };

        const estatusId = parseInt(res.data.estatus, 10) || 0;
        const status = statusMap[estatusId] || { title: "Desconocido", icon: "fa-question-circle", color: "#6b7280", text: "No fue posible determinar el estatus." };

        const folio = res.data.folio || "";
        const cliente = res.data.cliente_obj?.nombre || "";
        const fechaVenta = res.data.fecha_venta || "";
        const fechaUpdate = res.data.modified || "";

        resultado.html(`
          <div style="background: var(--ml-dark-surface); border: 1px solid var(--ml-glass-border); border-radius: var(--ml-radius); padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
              <div style="width: 56px; height: 56px; border-radius: 50%; background: ${status.color}22; display: flex; align-items: center; justify-content: center;">
                <i class="fas ${status.icon}" style="font-size: 1.5rem; color: ${status.color};"></i>
              </div>
              <div>
                <h4 style="margin: 0 0 4px; font-family: 'DM Sans', sans-serif; color: ${status.color};">${status.title}</h4>
                <p style="margin: 0; color: var(--ml-text-secondary); font-size: 0.9rem;">${status.text}</p>
              </div>
            </div>
            <div style="border-top: 1px solid var(--ml-glass-border); padding-top: 16px; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem;">
              ${folio ? `<div><span style="color: var(--ml-text-muted);">Nota:</span> <strong>${folio}</strong></div>` : ""}
              ${cliente ? `<div><span style="color: var(--ml-text-muted);">Cliente:</span> <strong>${cliente}</strong></div>` : ""}
              ${fechaVenta ? `<div><span style="color: var(--ml-text-muted);">Fecha:</span> <strong>${fechaVenta}</strong></div>` : ""}
            </div>
          </div>
        `).show();
      },
      error: function(){
        btn.prop("disabled", false).find("span").text("Consultar estatus");
        resultado.html(`
          <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--ml-radius-sm); padding: 16px; color: #ef4444;">
            <i class="fas fa-exclamation-circle"></i> Error al conectar. Intenta de nuevo.
          </div>
        `).show();
      }
    });
  });
});
</script>
