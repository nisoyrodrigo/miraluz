<?php
abstract class Model{

  public $tabla;
  public $validaciones = array();
  public $campos = array();
  private static $criteria = array(
    "SELECT" => array(),
    "JOIN"   => array(),
    "WHERE"  => array(),
    "GROUP"  => array(),
        "HAVING" => array(),
    "ORDER"  => "",
        "LIMIT"  => ""
  );
  private $llavePrimaria = "";
  public $module;
  private static $registros = array();

  public $error = "";

  private $mysqli;

  function __construct($id = null){

    require 'config/config.php';

    $this->mysqli = new \mysqli($config["DB"]["host"], $config["DB"]["user"], $config["DB"]["password"], $config["DB"]["db"]);
    
    if(!empty($this->mysqli->connect_error))
      echo "Error al conectar a la BD".$this->mysqli->error;
    
    mysqli_query($this->mysqli, "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
    
    $this->init();
    
    

    $resultado = mysqli_query($this->mysqli, "SHOW COLUMNS FROM ".$this->tabla) 
         or die($error = $this->mysqli->error);
  
    if(mysqli_num_rows($resultado) > 0)
    {
      while ($fila = mysqli_fetch_assoc($resultado)) {

        $tipo = trim(explode("(", $fila["Type"])[0]);

        switch($tipo){
          case "int":
            $tipo = "numeric";
            break;
          case "varchar":
            $tipo = "string";
            break;
        }

        if($fila["Key"] == "PRI"){
          $this->llavePrimaria = $fila["Field"];
        }

        $this->campos[$fila["Field"]] = array(
          "nombre"   => $fila["Field"],
          "tipo"     => $tipo,
          "longitud" => $this->obtenerCadena($fila["Type"], "(" , ")"),
          "noNulo"   => $fila["Null"] == "NO" && $fila["Key"] != "PRI" ? 1 : 0,
          "valor"    => $fila["Default"]
        );

        
      }

    }

    if($id != null){
      if(is_numeric($id)){
        $qry       = "SELECT * FROM ".$this->tabla." WHERE id = ".$id;
        
      }
      else{
        $qry       = "SELECT * FROM ".$this->tabla." ".$id;
      }
      $resultado = mysqli_query($this->mysqli, $qry);

      if(mysqli_num_rows($resultado) != 0){
        $row = mysqli_fetch_object($resultado);
        foreach($row as $atributo => $valor){
          $this->campos[$atributo]["valor"] = $valor;
          $this->$atributo = $valor;
        }
      }
      else{
        foreach($this->campos as $campo => $atributos){
          $this->$campo = $atributos["valor"];
        }
      }
      
    }
    else{
      foreach($this->campos as $campo => $atributos){
        $this->$campo = $atributos["valor"];
      }
    }

  }

  abstract protected function init();

  public function save($bandera = null){

    $bandera = false;
    $resultado = false;
    
    foreach($this->getAttributes() as $nombre => $valor)
    {
      if($bandera){
        if(!isset($this->campos[$nombre])){
          $this->error = "El campo ".$nombre." no se encuentra definido en la tabla ".$this->tabla;
        }
      }
      
      if($nombre == "campos")
        $bandera = true;
      
    }
    
    if($this->validate() && $this->error == ""){
      
      if($this->get($this->llavePrimaria) == 0){

        $campos = implode(",", array_keys($this->campos));
        $query  = "INSERT INTO ".$this->tabla." (".$campos.")";
        $query .= "VALUES (";

        foreach($this->campos as $campo){

          if($campo["nombre"] == "created"){
            $fecha = date("Y-m-d H:i:s");
            $query .= "'".$fecha."',";
            $this->set("created", $fecha);
          }
          else if($campo["nombre"] == "modified"){
            $query .= "'0000-00-00 00:00:00',";
            $this->set("modified", "0000-00-00 00:00:00");
          }
          else if($campo["nombre"] == "user" && empty($campo["valor"]) && $this->tabla != "user_section"){
            $query .= Motor::getUser($this->module)->id.",";
          }
          else if(($campo["valor"] === null || $campo["valor"] === "") && ($campo["tipo"] == "numeric" || $campo["tipo"] == "tinyint")){
            $query .= "NULL,";
          }
          else{
            $query .= "'".mysqli_real_escape_string($this->mysqli, $campo["valor"])."',";
          }
        }

        $query .= ",";
        $query = str_replace(",,", "", $query);
        $query .= ")";
        
        mysqli_query($this->mysqli, $query);

        if($this->mysqli->connect_errno || $this->mysqli->error != ""){
          $this->error .= "Error al insertar ". $this->mysqli->error. " \n ".$query;
        }
        else{
          $llavePrimaria = $this->llavePrimaria;
          $this->campos[$llavePrimaria]["valor"] = mysqli_insert_id($this->mysqli);
          $this->$llavePrimaria = mysqli_insert_id($this->mysqli);
          $resultado = true;
        }
      }
      else{
        $query  = "UPDATE ".$this->tabla;
        $query .= " SET ";
        foreach($this->campos as $campo){
          if($campo["nombre"] == "created"){
            
          }
          else if($campo["nombre"] == "modified"){
            $fecha = date('Y-m-d H:i:s');
            $query .= $campo["nombre"]. " = '".date('Y-m-d h:i:s')."',";
            $this->set("modified", $fecha);
          }
          else if($campo["nombre"] == "user" && empty($campo["valor"]) && $this->tabla != "user_section"){
            $query .= $campo["nombre"]. " = ".Motor::getUser($this->module)->id.",";
          }
          else if(($campo["valor"] === NULL || $campo["valor"] === "") && ($campo["tipo"] == "numeric" || $campo["tipo"] == "tinyint")){
            $query .= $campo["nombre"]. " = NULL,";
          }
          else{
            $query .= $campo["nombre"]. " = '".mysqli_real_escape_string($this->mysqli,$campo["valor"])."',";
          }
        }

        $query .= ",";
        $query = str_replace(",,", "", $query);

        $query .= " WHERE id = ".$this->get($this->llavePrimaria);
        
        
        
        mysqli_query($this->mysqli, $query);

        if($this->mysqli->connect_errno || $this->mysqli->error != ""){
          $this->error .= "Error al actualizar ".$this->mysqli->error. " \n ";
        }
        else{
          $resultado = true;
        }
        
        $query;
      }
    }

    return $resultado;
  }

  public function setAttributes($atributos){
    
    foreach($atributos as $atributo => $valor){
      if(in_array($atributo, array_keys($this->campos))){
        if($this->campos[$atributo]["tipo"] == "datetime"){
          $valor = str_replace("Z", "", str_replace("T", " ", $valor));
        }
        $this->campos[$atributo]["valor"] = $valor;
        $this->$atributo = $valor;
      }
    }
  }
  
  public function getAttributes(){
    
    $attributes = new stdClass();

    foreach($this->campos as $campo){
      $miNombre = $campo["nombre"];
      $attributes->$miNombre = $this->$miNombre;
    }

    return $attributes;
  }

  public function set($campo , $valor){
    $this->campos[$campo]["valor"] = $valor;
    $this->$campo = $valor;
  }

  public function get($campo){
    return $this->$campo;
  } 

  public function findAll($params = null){



    $registros = array();

    if($params == null){
      $query = "SELECT * FROM ".$this->tabla." t";
    }
    else if(is_array($params)){
      
      if(array_key_exists("campos", $params)) {
        $query = "SELECT ".implode(",", $params["campos"]). " FROM ".$this->tabla." t ";
      }
      else{
        $query = "SELECT * FROM $this->tabla t";
      }
      if(array_key_exists("condicion", $params)){

        $query .= " WHERE ";
         
        if(is_array($params["condicion"])){

          foreach($params["condicion"] as $llave => $valor){
            $query .= "$llave = '$valor' AND "; 
          }

          $query .= "AND";

          $query = str_replace("AND AND"," ", $query);

        }
        else{
          $query .= $params["condicion"];
        }

      }

      if(array_key_exists("group", $params)){

        if(is_array($params["group"])){
          $query .= implode(",", $params["group"]);
        }
        else{
          $query .= $params["group"];
        }
        
      }

      if(array_key_exists("limit", $params)){
        $query .= $params["limit"];
      }

    }
    else{
      $query = "SELECT * FROM ".$this->tabla." t ".$params;
    }
    
    $resultado = mysqli_query($this->mysqli, $query);

    while($row = mysqli_fetch_object($resultado)){
      $registros[] = $row;
    }
    
    return $registros;
  }


  public function getCount($params = null){



    $registros = array();

    if($params == null){
      $query = "SELECT * FROM ".$this->tabla." t";
    }
    else if(is_array($params)){
      
      if(array_key_exists("campos", $params)) {
        $query = "SELECT ".implode(",", $params["campos"]). " FROM ".$this->tabla." t ";
      }
      else{
        $query = "SELECT * FROM $this->tabla t";
      }
      if(array_key_exists("condicion", $params)){

        $query .= " WHERE ";
         
        if(is_array($params["condicion"])){

          foreach($params["condicion"] as $llave => $valor){
            $query .= "$llave = '$valor' AND "; 
          }

          $query .= "AND";

          $query = str_replace("AND AND"," ", $query);

        }
        else{
          $query .= $params["condicion"];
        }

      }

      if(array_key_exists("group", $params)){

        if(is_array($params["group"])){
          $query .= implode(",", $params["group"]);
        }
        else{
          $query .= $params["group"];
        }
        
      }

      if(array_key_exists("limit", $params)){
        $query .= $params["limit"];
      }

    }
    else{
      $query = "SELECT * FROM ".$this->tabla." t ".$params;
    }
    
    $resultado = mysqli_query($this->mysqli, $query);

    while($row = mysqli_fetch_object($resultado)){
      $registros[] = $row;
    }
    
    return count($registros);
  }

  public function findByPk($id, $tipoDato = "object"){

    $data = array();

    $query = "SELECT * FROM ".$this->tabla." WHERE ".$this->getCampoPK()." = ".$id;
    $resultado = mysqli_query($this->mysqli, $query);

    if(mysqli_num_rows($resultado) != 0){

      if($tipoDato == "array" || $tipoDato == "json"){
        $data = mysqli_fetch_assoc($resultado);

        if($tipoDato == "json"){
          $data = json_encode($data);
        }
      }
      else{
        $data = mysqli_fetch_object($resultado);
      }
    }
    else{

      foreach($this->campos as $campo){
        if($campo["tipo"] == "int")
          $data[$campo["nombre"]] = 0;
        else{
          $data[$campo["nombre"]] = "";
        }
      }

      if($tipoDato == "json"){
        $data = json_encode($data);
      }
      else if($tipoDato == "object"){
        $data = (object)$data;
      }
    }

    return $data;
  }

  public static function model(){
    $class = get_called_class();
    return new $class();
  }

  public function validate(){
  
    foreach($this->campos as $campo){
      $miNombre = $campo["nombre"];
      if($this->$miNombre != $campo["valor"]){
        
        $this->campos[$campo["nombre"]]["valor"] = $this->$miNombre; 
      }
    }

    $resultado = false;

    $validaciones = $this->validaciones;
    
    foreach($this->campos as $campo){
      
      
      

      $condiciones = array();

      $condiciones[] = $campo["tipo"];
      $condiciones[] = array(($campo["tipo"] == "enum" ? "enum" :"longitud") => $campo["longitud"]);
      
      if($campo["noNulo"] == 1)
        $condiciones[] = "noNulo";


      
      foreach($validaciones as $key => $valor){

        if(in_array($campo["nombre"], explode(",", $valor))){
          if(!in_array($key, $condiciones ))
            $condiciones[] = $key;
        }
      }

      $errorValidacion = "";
      
        

      if(in_array("noNulo", $condiciones) && ($campo['valor'] == "" || $campo["valor"] == null) && $campo['nombre'] != "created"){
        $errorValidacion = " no puede ser nulo";
      }
      else if(!empty($campo['valor'])){
        foreach($condiciones as $condicion){
          $errorValidacion = "";

          if(is_array($condicion))
          {
            if(isset($condicion["longitud"])){
              if(strlen($campo["valor"]) > $condicion["longitud"] && !empty($condicion["longitud"]) && $campo["valor"] != "NULL")
                $errorValidacion = " no puede contener m? de ".$condicion["longitud"]." caracteres";
            }
            else if(isset($condicion["enum"])){
              $datos = str_replace("'", "", $condicion["enum"]);
              $arrayEnum = explode(",", $datos);
    
              if(!in_array($campo["valor"], $arrayEnum))
                $errorValidacion = " solo puede tener los siguientes valor: ".$condicion["enum"];
            }
          }
          else{
            switch($condicion){
              case "mail":
                if(!$this->validaEmail($campo["valor"]))
                  $errorValidacion = " no es un Email v?ido";
                break;
              case "numerico":
                if(!is_numeric($campo["valor"]))
                  $errorValidacion = " no es un N?mero v?ido";
                break;
              case "soloLetras":
                if(!ctype_alpha($campo["valor"]))
                  $errorValidacion = " solo puede contener letras";
                break;
              
            }
          }
        }
      }
      
      

      if($errorValidacion != "")
        $this->error .= "<br/> * El campo ".$campo["nombre"].$errorValidacion;
      
    }
    
    if($this->error == ""){
      $resultado = true;
    }

    return $resultado;
  }

  public function getCampoPK(){
    return "id";
  }
  
  public function remove(){
    $result = false;
    $query = "DELETE FROM ".$this->tabla." WHERE ".$this->llavePrimaria." = ".$this->get($this->llavePrimaria);
    $result = mysqli_query($this->mysqli, $query);
    if(mysqli_error($this->mysqli)){
      $this->error = "error al tratar de remover registro ".mysqli_error($this->mysqli)." ".$query;
    }
    else{
      $result = true;
    }
    return $result;
  }

  public function removeByPk($id){
    $result = false;
    $query = "DELETE FROM ".$this->tabla." WHERE ".$this->llavePrimaria." = ".$id;
    $result = mysqli_query($this->mysqli, $query);
    if(mysqli_error($this->mysqli)){
      $this->error = "error al tratar de remover registro ".mysqli_error($this->mysqli)." ".$query;
    }
    else{
      $result = true;
    }
    return $result;
  }

  public function executeQuery($qry, $tipoDato = "object"){
    
    $data = array();

    $resultado = mysqli_query($this->mysqli, $qry);

    if(mysqli_error($this->mysqli)){
      $data = $this->error = mysqli_error($this->mysqli);
    }
    
    if($this->error == ""){
      if($tipoDato == "object"){
        while($row = mysqli_fetch_object($resultado)){
          $data[] = $row;
        }
      }
      else{
        while($row = mysqli_fetch_assoc($resultado)){
          $data[] = $row;
        }
      }

      if($tipoDato == "json"){
        $data = json_encode($data);
      }
    }
    
    return $data;
  }
  
  public function executeNonQuery($qry){
    
    $resultado = mysqli_query($this->mysqli, $qry);

    if(mysqli_error($this->mysqli)){
      $this->error = mysqli_error($this->mysqli);
    }
  
    return $this->error;
  }

  public function executeQueryPager($query, $params){

    $resultado     = array("resultado" => false);
    $pagina        = isset($params["Pagina"]) ? $params["Pagina"] - 1 : 0;
    $ordenCriterio = isset($params["OrdenCriterio"]) ? explode(",", $params["OrdenCriterio"]) : "";
    $ordenamiento  = "";
    $buscar        = isset($params["Buscar"]) ? $params["Buscar"] : "";
    $busqueda      = "";
    $registros     = array();
    $maxRegistros  = isset($params["nRenglones"]) ? $params["nRenglones"] : 0;
    $tipoDato      = isset($params["tipoDato"]) ? $params["tipoDato"] : "object";
    
    if($pagina < 0) $pagina = 0;
    
    if($ordenCriterio != "" && count($ordenCriterio)!=0)
    {
      foreach($ordenCriterio as $campo){
        if($campo != ""){
          $criterio      = explode("_", $campo);
          $ordenamiento .= $criterio[0] == "A" ? $criterio[1]." ASC," : $criterio[1]." DESC,";
        }
      }
      $ordenamiento = substr($ordenamiento, 0, -1);
    }

    if($buscar != ""){

      $tiposFecha = isset($buscar["tipos"]["fecha"]) ? $buscar["tipos"]["fecha"] : array();
      $tiposIn   = isset($buscar["tipos"]["in"]) ? $buscar["tipos"]["in"] : array();

      foreach($buscar as $indice => $valor){
        $campos   = explode(",", $indice);
        
        if($indice != "tipos"){
          if($valor != ""){
            $busqueda .= " AND (";
            foreach($campos as $campo){

              if(in_array($campo, array_keys($tiposFecha)))
                $busqueda .= "DATE(".$campo.") = date(str_to_date('".$valor."', '".$tiposFecha[$campo]."')) OR ";
              else if(in_array($campo, $tiposIn))
                $busqueda .= $campo." in ('".$valor."') OR ";
              else
                $busqueda .= $campo." like '%".$valor."%' OR ";
            }
            $busqueda = substr($busqueda, 0, -3);
            $busqueda .= ") ";
          }
        }
      }
    }

    if($busqueda != ""){
      if(strpos($query, "where") || strpos($query, "WHERE")){
        $query .= $busqueda;
      }
      else{
        $query .= "WHERE 1 ".$busqueda;
      }
    }
    
    if($ordenamiento != "" && strtoupper(str_replace(" ","",$ordenamiento)) != "DESC" && strtoupper(str_replace(" ","",$ordenamiento)) != "ASC" ){
      if(strpos(strtoupper($query), "ORDER BY") ){
        $query .= ", ".$ordenamiento;
      }
      else{
        $query .= " ORDER BY ".$ordenamiento;
      }
    }
    
    $resTotal = mysqli_query($this->mysqli, $query);

    if(mysqli_error($this->mysqli)){
      $resultado["error"] = mysqli_error($this->mysqli);
    }
    else{

      $total    = mysqli_num_rows($resTotal);

      $limit = "";

      if($maxRegistros != 0){
        $limit = " LIMIT ".($pagina * $maxRegistros).", ".$maxRegistros;
      }

      $resQuery = mysqli_query($this->mysqli, $query.$limit);

      if(mysqli_error($this->mysqli)){
        $resultado["error"] = mysqli_error($this->mysqli);
      }
      else{
        $resultado["data"] = array();

        if($tipoDato == "array" || $tipoDato == "json"){
          while($row = mysqli_fetch_assoc($resQuery)){
            $resultado["data"][] = $row;
          }
        }
        else{

          while($row = mysqli_fetch_object($resQuery)){
            $resultado["data"][] = $row;
          }
        }

        if($tipoDato == "json"){
          $resultado["data"] = json_encode($resultado["data"]);
        }

        $resultado["total"]  = $total;
        $resultado["resultado"] = true;
      }
    }

    return $resultado;
  }

  private function validaEmail($email){ 
       $mail_correcto = 0; 
       //compruebo unas cosas primeras 
       if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){ 
          if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) { 
             //miro si tiene caracter . 
             if (substr_count($email,".")>= 1){ 
                //obtengo la terminacion del dominio 
                $term_dom = substr(strrchr ($email, '.'),1); 
                //compruebo que la terminaci? del dominio sea correcta 
                if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){ 
                     //compruebo que lo de antes del dominio sea correcto 
                     $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1); 
                     $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1); 
                     if ($caracter_ult != "@" && $caracter_ult != "."){ 
                        $mail_correcto = 1; 
                     }   
                } 
             } 
          } 
       } 
       if ($mail_correcto) 
          return 1; 
       else 
          return 0; 
  }

  private function obtenerCadena($contenido, $inicio, $fin, $completo = false){
      $r = explode($inicio, $contenido);
      if (isset($r[1])){
          $r = explode($fin, $r[1]);

          if($completo)
            return $inicio.$r[0].$fin;
          else
            return $r[0];
      }
      return '';
  }

  /* Metodos de Renderizacion de Formularios  */

  public function renderSelect($campoIndex, $campoValue, $valorSelect, $holder, $atributos = null, $extra = "", $data = null){

    echo "<select ";
        
    foreach($atributos as $atributo => $valor){
      echo $atributo.'="'.$valor.'" ';
    }

    echo ">";
    echo '<option value=""></option>';

    if($data == null){
    
      $qry = "SELECT $campoIndex, $campoValue FROM ".$this->tabla." ".$extra;

      $resultado = mysqli_query($this->mysqli, $qry);
      if(mysqli_error($this->mysqli)){
        $this->error .= mysqli_error();
      }
      else{
        while($row = mysqli_fetch_array($resultado)){
          $select = "";
          if($row[0] == $valorSelect)
            $select = "selected";
          echo '<option value="'.$row[0].'" '.$select.'>'.$row[1].'</option>';
        }
      }
    }
    else{
      foreach($data as $row){
        $select = "";
        if($row[$campoIndex] == $valorSelect)
          $select = "selected";
        echo '<option value="'.$row[$campoIndex].'" '.$select.'>'.$row[$campoValue].'</option>';
      }
    }

    echo "</select>";
    
  }

  public function getFieldsFromData($fields, $data, $tipoDato = "object"){  

    $campos = array();

    if($tipoDato == "object"){
      $fields = explode(",", $fields);
      foreach($data as $row){
        if(count($fields) == 1)
          $campos[] = $row->$fields[0];
        else{
          $array = array();
          foreach($fields as $field){
            $array[$field] = $row->$field;
          }
          
          $campos[] = $array;
        }
      }
    }

    return $campos;
  }

  /* Fin */

}
?>