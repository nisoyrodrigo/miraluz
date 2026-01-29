<?

$aQuery  = "SELECT id, nombre, razon_social, telefono, correo FROM ec_cliente e WHERE e.id != 1 AND e.estatus = 1 ";
$queryTerms = explode(' ', $query); // Divide la consulta en palabras individuales

$conditions = [];
foreach ($queryTerms as $term) {
  $term = trim($term);
  if (!empty($term)) {
    $conditions[] = "(e.nombre LIKE '%$term%' OR e.correo LIKE '%$term%' OR e.telefono LIKE '%$term%')";
  }
}

if (count($conditions) > 0) {
  $aQuery .= " AND (" . implode(" OR ", $conditions) . ")";
}

$aQuery .= " LIMIT 20 ";

$clientes = Cliente::model()->executeQuery($aQuery);

if(count($clientes) == 0){
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