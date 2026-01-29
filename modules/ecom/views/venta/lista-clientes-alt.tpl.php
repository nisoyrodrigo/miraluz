<?

// Inicializa las condiciones básicas
$aQuery = "SELECT id, nombre, razon_social, telefono, correo FROM ec_cliente e WHERE e.id != 1 AND e.estatus = 1 ";

$conditions = [];

// Agrega condiciones basadas en las variables existentes
if (!empty($nombre)) {
    $conditions[] = "e.nombre LIKE '%$nombre%'";
}

if (!empty($telefono)) {
    $conditions[] = "e.telefono LIKE '%$telefono%'";
}

if (!empty($correo)) {
    $conditions[] = "e.correo LIKE '%$correo%'";
}

// Combina las condiciones si hay alguna
if (count($conditions) > 0) {
    $aQuery .= " AND (" . implode(" OR ", $conditions) . ")";
}

// Agrega un límite para evitar demasiados resultados
$aQuery .= " LIMIT 20";

// Ejecuta la consulta
$clientes = Cliente::model()->executeQuery($aQuery);

// Si no hay resultados
if (count($clientes) == 0) {
    echo '<p class="text-warning">No se encontró ninguna coincidencia.</p>';
}

foreach ($clientes as $key => $value) {?>

  <div class="col-6 mb-4">
    <div class="card-cliente">
      <h4><i class="fas fa-user"></i> <?=$value->nombre?></h4><br>
      <h5><i class="fas fa-phone"></i> <?=$value->telefono?></h5>
      <h5><i class="fas fa-envelope"></i> <?=$value->correo?></h5><br><br>
      <button type="button" class="btn btn-primary float-end" onclick="seleccionarCliente(<?=$value->id?>, '<?=$value->nombre?>')">Seleccionar</button>
    </div>
  </div>


<?}?>