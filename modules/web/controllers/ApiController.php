<?php
  class ApiController extends Controller{
    
    public function actionInit(){
    }

    public function actionGetData(){
      $this->template = null;
      $aSalida = array();
      $model = new CodigoPostal("WHERE codigo_postal = '".$this->params["valor"]."'");
      $estado = new Estado($model->estado);
      $model->estado_nombre = $estado->nombre;
      $colonias = CodigoPostal::model()->executeQuery("SELECT colonia FROM ec_codigos_postales WHERE codigo_postal = '".$this->params["valor"]."'");/*
      foreach ($colonias as $key => $value) {
        $aColonias .= "<option value=\"".$value->colonia."\">".$value->colonia."</option>";
      }

      $aColonias .= "<option value=\"SIN COLONIA\">No está en las opciones</option>";*/

      $aMunicipios = "";
      $municipios = CodigoPostal::model()->executeQuery("SELECT municipio FROM ec_codigos_postales WHERE estado = '".$estado->id."' GROUP BY municipio");
      foreach ($municipios as $key => $value) {
        $aMunicipios .= "<option value=\"".$value->municipio."\" ".(($value->municipio == $model->municipio) ? "selected":"").">".$value->municipio."</option>";
      }

      $objN = new MunicipioSinVacante("WHERE municipio = '".$model->municipio."' AND estado = ".$model->estado);
      $aNoVacantes = "si";
      if($objN->id != ""){
        $aNoVacantes = "no";
      }


      //$colonias = CodigoPostal::model()->findAll("WHERE estado = ".$estado->id."  AND municipio = '".$_SESSION["r_municipio"]."' GROUP BY colonia");

      $aSalida["estado"] = $model;
      $aSalida["aux"] = "SELECT municipio FROM ec_codigos_postales WHERE estado = '".$estado->id."' GROUP BY municipio";
      $aSalida["colonias"] = $colonias;
      $aSalida["municipios"] = $aMunicipios;
      $aSalida["vacantes"] = $aNoVacantes;
      $this->renderJSON($aSalida);
    }


    public function actionGetData2(){
      $this->template = null;
      $aSalida = array();
      $model = new Estado($this->params["valor"]);
      $model->estado_nombre = $estado->nombre;/*
      $colonias = CodigoPostal::model()->executeQuery("SELECT colonia FROM ec_codigos_postales WHERE codigo_postal = '".$this->params["valor"]."'");
      foreach ($colonias as $key => $value) {
        $aColonias .= "<option value=\"".$value->colonia."\">".$value->colonia."</option>";
      }

      $aColonias .= "<option value=\"SIN COLONIA\">No está en las opciones</option>";*/

      $aMunicipios = "";
      $municipios = CodigoPostal::model()->executeQuery("SELECT municipio FROM ec_codigos_postales WHERE estado = '".$model->id."' GROUP BY municipio");
      foreach ($municipios as $key => $value) {
        $aMunicipios .= "<option value=\"".$value->municipio."\">".$value->municipio."</option>";
      }

      $aSalida["estado"] = $model;
      //$aSalida["colonias"] = $aColonias;
      $aSalida["municipios"] = $aMunicipios;
      $this->renderJSON($aSalida);
    }


    public function actionGetPreguntas(){
      $this->template = null;
      $this->render("preguntas", array("puesto"=>$this->params["puesto"]));
    }

    public function actionGetTiendas(){
      $this->template = null;
      $sHtml = "";
      $sHtml .= '<option value="">Selecciona una tienda...</option>';
      $rows = PlazaTienda::model()->findAll("WHERE plaza = ".$this->params["plaza"]." ORDER BY nombre ASC");
      foreach ($rows as $key => $value) {
        $sHtml .= '<option value="'.$value->id.'">'.$value->cr."  –  ".$value->nombre.'</option>';
      }
      $this->renderJSON($sHtml);
    }

    public function actionRevisaRedireccion(){
      $aSalida = array();
      $aPlazas = "";
      $sep = "";
      $auuxWhere = " AND p.es_cedis = 0 ";
      if($this->params["puesto_elegido"] == "2"){
        $auuxWhere = " AND p.es_cedis = 1 ";
        $objPlazas = PlazaMunicipio::model()->executeQuery("SELECT pm.id, pm.plaza, pm.estado, pm.municipio FROM ec_plaza_municipio pm LEFT JOIN ec_plaza p ON pm.plaza = p.id WHERE pm.estado = '".$this->params["estado"]."' AND pm.municipio = '".$this->params["municipio"]."' $auuxWhere");
        foreach ($objPlazas as $key => $value) {
          $aPlazas .= $sep.$value->plaza;
          $sep = ",";
        }

        if($aPlazas == ""){
          $puesto_elegido = "1";
        }
      }

      $sep = "";
      if($this->params["puesto_elegido"] != "2" || $aPlazas == ""){
        //$puesto_elegido = "1";
        $auuxWhere = " AND p.es_cedis = 0 ";
        $objPlazas = PlazaMunicipio::model()->executeQuery("SELECT pm.id, pm.plaza, pm.estado, pm.municipio FROM ec_plaza_municipio pm LEFT JOIN ec_plaza p ON pm.plaza = p.id WHERE pm.estado = '".$this->params["estado"]."' AND pm.municipio = '".$this->params["municipio"]."' $auuxWhere");
        foreach ($objPlazas as $key => $value) {
          $aPlazas .= $sep.$value->plaza;
          $sep = ",";
        }
      }

      $oplazas = explode(",", $aPlazas);
      $enCasa = "1";
      $auxPlazas = Plaza::model()->findAll("WHERE id IN(".$aPlazas.")");
      foreach ($auxPlazas as $key => $value) {
        if($value->en_casa != "1"){
          $enCasa = "2";
        }
      }

      $_SESSION["r_estado"] = $this->params["estado"];
      $_SESSION["r_municipio"] = $this->params["municipio"];
      $_SESSION["r_cp"] = $this->params["codigo_postal"];
      $_SESSION["r_puesto"] = $this->params["puesto_elegido"];
 
      $plaza = new Plaza($oplazas[0]);
      $aSalida["plaza"] = $plaza;
      $aSalida["link"] = ($enCasa == "1") ? "" : "http://www.oxxoreclutamiento.com/";
      $aSalida["exito"] = ($this->error == "") ? true:false;

      $this->renderJSON($aSalida);
    }

  }
?>