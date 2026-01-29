<?php
  class DefaultController extends Controller{
    
    public function actionInit(){
      //if($this->user->rol == 13){
      $this->render("index");
    }

    public function actionInit2(){
      $this->render("index2");
    }

    public function ActionReparaPaquetes(){
      $this->template = null;
      $rows = PublicidadPaquetePlaza::model()->findAll("WHERE fecha = '2021-04-01'");
      foreach ($rows as $key => $value) {
        $model = new PublicidadPaquetePlaza("WHERE plaza = ".$value->plaza." AND fecha = '2021-05-01'"); 
        $model->paquete = $value->paquete;
        $model->plaza = $value->plaza;
        $model->fecha = "2021-05-01";
        $model->usuario = $value->usuario;
        $model->estatus = 'pendiente';

        if($model->id == ""){
          if(!$model->save()){
            $this->error = $model->error;
          } else {
            $dataModel = $model->getAttributes();
          }
          if($this->error == ""){
            $paquete = new PublicidadPaquete($model->paquete);
            if($paquete->publicaciones > 0){
              for ($i=0; $i < $paquete->publicaciones; $i++) { 
                $nombre = "Publicación ".($i+1)." de ".$paquete->publicaciones;
                $amodel = new PublicidadPaquetePlazaPublicacion();
                $amodel->nombre = $nombre;
                $amodel->paquete_plaza = $dataModel->id;
                $amodel->fecha = "NULL";
                $amodel->save(); 
              }
            }
          }
        }
      }
      $this->renderJSON($rows);
    }

    public function actionActualizaInformacion(){
      $this->template = null;
      $this->render("edit-informacion");
    }

    public function actionGraficaContratacionesV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");

      foreach ($plazas as $key => $value) {
        
        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_contratacion pc
        LEFT JOIN ec_prospecto p ON pc.prospecto = p.id
        WHERE pc.plaza = '".$value->id."'
        ";
        $auxQuery .= $auxWhere;
        $contratados = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($contratados);
        $aData['query'] = $auxQuery;
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaContrataciones(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");
      $today = date('Y-m-d', time());
      foreach ($plazas as $key => $value) {
        
        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
        }

        

        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT contratado_dia AS total 
          FROM ec_plaza_corte pc
          WHERE pc.plaza = '".$value->id."'
          ";

          $auxQuery .= $auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto_contratacion p WHERE DATE_FORMAT(p.fecha_registro, '%Y-%m-%d') = '$today' AND p.plaza = ".$svalue->id."
          ";
          //echo $auxQuery;
          //exit;
        }
        $contratados = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($contratados);
        $aData['query'] = $auxQuery;
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaContratacionesZonaV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }
      $zonas = Zona::model()->findAll($aWhere);


      foreach ($zonas as $key => $value) {
        $contratados = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha_inicio"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
          }
          if($this->params["fecha_fin"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
          }
          if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
            $auxWhere = " AND DATE_FORMAT(pc.fecha_registro, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
          }
         
          $auxQuery = "
          SELECT IFNULL(COUNT(*),0) AS total 
          FROM ec_prospecto_contratacion pc
          LEFT JOIN ec_prospecto p ON pc.prospecto = p.id
          WHERE pc.plaza = '".$svalue->id."'
          ";
          $auxQuery .= " ".$auxWhere;
          $aContratados = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $contratados += $aContratados;
          $aSalida["plazas"][$svalue->id] = $auxQuery;
        }
        
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($contratados);
        $aData["query"] = $auxQuery;
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaPostulacionesPlaza(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }
      $plazas = PlazaQS::model()->findAll($aWhere);
      $today = date('Y-m-d', time());
      $contratados = 0;
      foreach ($plazas as $skey => $svalue) {
        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
        }
       
        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT COUNT(*) AS total 
          FROM ec_postulacion p
          WHERE p.plaza = '".$svalue->id."'
          ";
          $auxQuery .= " ".$auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_postulacion p WHERE DATE_FORMAT(p.fecha, '%Y-%m-%d') = '$today' AND p.plaza = ".$svalue->id."
          ";
          //echo $auxQuery;
          //exit;
        }

        $aContratados = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $contratados += $aContratados;
        $aSalida["plazas"][$svalue->id] = $auxQuery;


        $aData = array();
        $aData['label'] = $svalue->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($contratados);
        $aData["query"] = $auxQuery;
        $aSalida["data"][] = $aData;


      }
      
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnProcesoV2(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.fecha_contratado, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.fecha_contratado, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(p.fecha_contratado, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_plaza pp 
        LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
        WHERE pp.plaza = ".$value->id." AND p.estatus IN(3,5,6,8,10)
        ";
        //$auxQuery .= $auxWhere;
        //$this->error = $auxQuery;
        $enproceso = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($enproceso);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnProceso(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");
      $today = date('Y-m-d', time());

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
        }


        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT proceso_dia AS total 
          FROM ec_plaza_corte pc
          WHERE pc.plaza = '".$value->id."'
          ";
          $auxQuery .= $auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 0 AND p.estatus IN(3,5,6,8,10) AND plaza_asignada = ".$value->id."
          ";
          //echo $auxQuery;
          //exit;
        }
        //$this->error = $auxQuery;
        $enproceso = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($enproceso);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnProcesoZonaV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {

        $enproceso = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha_inicio"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
          }
          if($this->params["fecha_fin"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
          }
          if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
            $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
          }
          $auxQuery = "
          SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
          FROM ec_prospecto_plaza pp 
          LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
          WHERE pp.plaza = ".$svalue->id." AND p.estatus IN(3,5,6,8,10)
          ";
          $auxQuery .= " ".$auxWhere;
          $aEnProceso = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $enproceso += $aEnProceso;
        }
        
        $aData = array();
        $aData['query'] = $auxQuery;
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($enproceso);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnProcesoZona(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $today = date('Y-m-d', time());

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {

        $enproceso = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
          }
          if($this->params["fecha"] != $today){
            $auxQuery = "
            SELECT pc.proceso_dia AS total 
            FROM ec_plaza_corte pc
            WHERE pc.plaza = ".$svalue->id." 
            ";
            $auxQuery .= " ".$auxWhere;
          } else {
            $auxQuery = "
              SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 0 AND p.estatus IN(3,5,6,8,10) AND plaza_asignada = ".$svalue->id."
            ";
            //echo $auxQuery;
            //exit;
          }
          
          $aEnProceso = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $enproceso += $aEnProceso;
        }
        
        $aData = array();
        $aData['query'] = $auxQuery;
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($enproceso);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaSinAsignarV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");

      foreach ($plazas as $key => $value) {

        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_plaza pp 
        LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
        WHERE pp.plaza = ".$value->id." AND p.estatus = '0' AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0
        ";
        $auxQuery .= " ".$auxWhere;

        //$this->error = $auxQuery;

        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaSinAsignar(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");
      $today = date('Y-m-d', time());

      foreach ($plazas as $key => $value) {

        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') >= '".$this->params["fecha"]."'";
        }

        

        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT sin_asignar_dia AS total 
          FROM ec_plaza_corte pc
          WHERE pc.plaza = '".$value->id."'
          ";
          $auxQuery .= $auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE p.es_cartera = 0 AND p.reclutador_user IS NULL AND pp.plaza = ".$value->id."
          ";
          //echo $auxQuery;
          //exit;
        }


        //$this->error = $auxQuery;

        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaSinAsignarZonaV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {
      
        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha_inicio"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
          }
          if($this->params["fecha_fin"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
          }
          if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
            $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
          }
          $auxQuery = "
          SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
          FROM ec_prospecto_plaza pp 
          LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
          WHERE pp.plaza = ".$svalue->id." AND p.estatus = 0 AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0
          ";
          $auxQuery .= " ".$auxWhere;
          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['query'] = $auxQuery;
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;


      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaSinAsignarZona(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $today = date('Y-m-d', time());

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {
      
        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') >= '".$this->params["fecha"]."'";
          }
          
          if($this->params["fecha"] != $today){
            $auxQuery = "
            SELECT pc.sin_asignar_dia AS total 
            FROM ec_plaza_corte pc 
            WHERE pc.plaza = ".$svalue->id."
            ";
            $auxQuery .= " ".$auxWhere;
          } else {
            $auxQuery = "
              SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE p.es_cartera = 0 AND p.reclutador_user IS NULL AND pp.plaza = ".$svalue->id."
            ";
            //echo $auxQuery;
            //exit;
          }

          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['query'] = $auxQuery;
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;


      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnCarteraV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_plaza pp 
        LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
        WHERE pp.plaza = ".$value->id." AND p.estatus = 0 AND p.reclutador_user IS NULL AND p.es_cartera = 1
        ";
        $auxQuery .= " ".$auxWhere;
        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnMiCarteraV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_plaza pp 
        LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
        WHERE pp.plaza = ".$value->id." AND p.estatus = 0 AND p.reclutador_user IS NOT NULL AND p.es_cartera = 1 AND p.cartera_tipo = 1
        ";
        $auxQuery .= " ".$auxWhere;
        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        //$this->error = "SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza = ".$value->id." AND p.estatus = 0 AND p.reclutador_user IS NULL";
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }



    public function actionGraficaEnCartera(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");
      $today = date('Y-m-d', time());

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
        }


        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT cartera_general_dia AS total 
          FROM ec_plaza_corte pc
          WHERE pc.plaza = '".$value->id."'
          ";
          $auxQuery .= $auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 1 AND p.cartera_tipo = 0 AND plaza_asignada = ".$value->id."
          ";
          //echo $auxQuery;
          //exit;
        }
        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnMiCartera(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $plazas = Plaza::model()->findAll("WHERE id IN($sPlazas)");
      $today = date('Y-m-d', time());

      foreach ($plazas as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha"] != ""){
          $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') = '".$this->params["fecha"]."'";
        }

        

        if($this->params["fecha"] != $today){
          $auxQuery = "
          SELECT mi_cartera_dia AS total 
          FROM ec_plaza_corte pc
          WHERE pc.plaza = '".$value->id."'
          ";
          $auxQuery .= $auxWhere;
        } else {
          $auxQuery = "
            SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 1 AND p.cartera_tipo = 1 AND plaza_asignada = ".$value->id."
          ";
          //echo $auxQuery;
          //exit;
        }
        $sinasignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        //$this->error = "SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza = ".$value->id." AND p.estatus = 0 AND p.reclutador_user IS NULL";
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnCarteraZonaV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {

        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha_inicio"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
          }
          if($this->params["fecha_fin"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
          }
          if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
            $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
          }
          $auxQuery = "
          SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
          FROM ec_prospecto_plaza pp 
          LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
          WHERE pp.plaza = ".$svalue->id." AND p.estatus = 0 AND p.reclutador_user IS NULL AND p.es_cartera = 1
          ";
          $auxQuery .= " ".$auxWhere;
          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnCarteraZona(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $today = date('Y-m-d', time());

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);

      foreach ($zonas as $key => $value) {

        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') >= '".$this->params["fecha"]."'";
          }
          


          if($this->params["fecha"] != $today){
            $auxQuery = "
            SELECT pc.cartera_general_dia AS total 
            FROM ec_plaza_corte pc
            WHERE pc.plaza = ".$svalue->id." 
            ";
            $auxQuery .= " ".$auxWhere;
          } else {
            $auxQuery = "
              SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 1 AND p.cartera_tipo = 0 AND plaza_asignada = ".$svalue->id."
            ";
            //echo $auxQuery;
            //exit;
          }
          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnMiCarteraZonaV1(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);


      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);
      

      foreach ($zonas as $key => $value) {

        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha_inicio"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
          }
          if($this->params["fecha_fin"] != ""){
            $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
          }
          if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
            $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
          }
          $auxQuery = "
          SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
          FROM ec_prospecto_plaza pp 
          LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
          WHERE pp.plaza = ".$svalue->id." AND p.estatus = 0 AND p.reclutador_user IS NOT NULL AND p.es_cartera = 1 AND p.cartera_tipo = 1
          ";
          $auxQuery .= " ".$auxWhere;
          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionGraficaEnMiCarteraZona(){
      $this->template = null;
      $aSalida = array();
      $sPlazas = implode(",", $this->params["plazas"]);
      $today = date('Y-m-d', time());

      if($sPlazas == "0"){
        $aWhere = "WHERE 1 = 1";
      } else {
        $aWhere = "WHERE id IN($sPlazas)";
      }

      $zonas = Zona::model()->findAll($aWhere);
      

      foreach ($zonas as $key => $value) {

        $sinasignar = 0;
        $plazas = Plaza::model()->findAll("WHERE zona = '".$value->id."'");
        foreach ($plazas as $skey => $svalue) {
          $auxWhere = "";
          if($this->params["fecha"] != ""){
            $auxWhere .= " AND DATE_FORMAT(pc.fecha, '%Y-%m-%d') >= '".$this->params["fecha"]."'";
          }
          

          if($this->params["fecha"] != $today){
            $auxQuery = "
            SELECT pc.mi_cartera_dia AS total 
            FROM ec_plaza_corte pc
            WHERE pc.plaza = ".$svalue->id." 
            ";
            $auxQuery .= " ".$auxWhere;
          } else {
            $auxQuery = "
              SELECT IFNULL(count(*), 0) AS total FROM ec_prospecto p WHERE p.es_cartera = 1 AND p.cartera_tipo = 1 AND plaza_asignada = ".$svalue->id."
            ";
            //echo $auxQuery;
            //exit;
          }
          $aSinAsignar = Prospecto::model()->executeQuery($auxQuery)[0]->total;
          $sinasignar += $aSinAsignar;
        }
        
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($sinasignar);
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }


    public function actionGraficaMediosDifusion(){
      $this->template = null;
      $aSalida = array();
      $sMedios = implode(",", $this->params["medios"]);
      $medios = MedioDifusion::model()->findAll("WHERE id IN($sMedios)");

      $apWhere = "";
      $apSep = "";
      $auxPlazas = " 1 = 1 ";
      foreach ($this->params["plazas"] as $key => $value) {
        $apWhere .= $apSep.$value;
        $apSep = ",";
      }

      if($apWhere != ""){
        $auxPlazas = "pp.plaza IN($apWhere)";
      }

      foreach ($medios as $key => $value) {
        $auxWhere = "";
        if($this->params["fecha_inicio"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') >= '".$this->params["fecha_inicio"]."'";
        }
        if($this->params["fecha_fin"] != ""){
          $auxWhere .= " AND DATE_FORMAT(p.created, '%Y-%m-%d') <= '".$this->params["fecha_fin"]."'";
        }
        if($this->params["fecha_inicio"] != "" && $this->params["fecha_inicio"] == $this->params["fecha_fin"]){
          $auxWhere = " AND DATE_FORMAT(p.created, '%Y-%m-%d') = '".$this->params["fecha_inicio"]."'";
        }
        $auxQuery = "
        SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total 
        FROM ec_prospecto_plaza pp 
        LEFT JOIN ec_prospecto p ON pp.prospecto = p.id
        WHERE $auxPlazas AND p.medio_difusion = ".$value->id."
        ";
        $auxQuery .= " ".$auxWhere;
        $total = Prospecto::model()->executeQuery($auxQuery)[0]->total;
        //$total = Prospecto::model()->executeQuery("SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE medio_difusion = ".$value->id. " AND (".$apWhere.")")[0]->total;
        //$this->error = "SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE medio_difusion = ".$value->id. " AND (".$apWhere.")";
        $aData = array();
        $aData['label'] = $value->nombre;
        $aData['backgroundColor'] = $this->randomColor();
        $aData['data'] = array($total);
        $aData["query"] = $auxQuery;
        $aSalida["data"][] = $aData;
      }
      $this->renderJSON($aSalida);
    }

    public function actionSaveInformacion(){
      $this->template = null;
      $model = new User($this->user->id);
      $reclutador = new Reclutador("WHERE user = ".$model->id);
      if(!empty($this->params["telefono"])){
        $reclutador->telefono = $this->params["telefono"];
      }
      if(!empty($this->params["fecha_nacimiento"])){
        $reclutador->fecha_nacimiento = $this->params["fecha_nacimiento"];
      }
      if(!$reclutador->save()){
        $this->error .= "Error al actualizar información.";
      }
      if(!empty($this->params["password_actual"]) && !empty($this->params["password_nuevo"]) && !empty($this->params["password_nuevo_confirma"]) ){
        if($this->params["password_nuevo"] != $this->params["password_nuevo_confirma"]){
          $this->error .= "El password nuevo no coincide con su confirmación.";
        } else {
          $auxUser = new User("WHERE id = ".$model->id." AND password = SHA2('".$this->params["password_actual"]."',256)");
          if($auxUser->id==""){
            $this->error .= "Tu password actual no es correcto.";
          } else {
            $aModel = User::model()->executeQuery("UPDATE user SET password = SHA2('".$this->params["password_nuevo"]."',256) WHERE id = ".$model->id);
          }
        }
      }
      $this->renderJSON($model);
    }

    public function actionGeneraPlazas(){
      $this->template = null;
      return;
      $rows = Prospecto::model()->executeQuery("SELECT p.id AS ide, p.nombre, p.apaterno, pp.id, p.plazas FROM ec_prospecto p LEFT JOIN ec_prospecto_plaza pp ON p.id = pp.prospecto WHERE pp.id IS NULL AND p.plazas != ''");
      foreach ($rows as $key => $value) {
        $plazas = explode(",", $value->plazas);
        foreach ($plazas as $skey => $svalue) {
          $model = new ProspectoPlaza("WHERE prospecto = ".$value->ide." AND plaza = ".$svalue);
          $model->prospecto = $value->ide;
          $model->plaza = $svalue;
          $model->save();
        }
      }
      $this->renderJSON($rows);
    }

    public function actionGeneraRegistros(){
      $this->template = null;
      return;
      $rows = Prospecto::model()->executeQuery("SELECT id, nombre, apaterno, amaterno, plazas, created FROM ec_prospecto");
      foreach ($rows as $key => $value) {
        $model = new ProspectoRegistro();
        $model->prospecto = $value->id;
        $model->fecha_registro = date('Y-m-d H:i:s', strtotime($value->created));
        $model->save();
      }
      $this->renderJSON($rows);
    }

    public function actionGeneraContrataciones(){
      $this->template = null;
      return;
      $rows = Prospecto::model()->findAll("WHERE estatus = 11");
      foreach ($rows as $key => $value) {
        $reclutador = new Reclutador("WHERE user = ".$value->reclutador_user);
        $model = new ProspectoContratacion();
        $model->prospecto = $value->id;
        $model->fecha_registro = date('Y-m-d H:i:s', strtotime($value->fecha_contratado));
        $model->plaza = $reclutador->plazas;
        $model->reclutador_user = $value->reclutador_user;
        $model->save();
      }
      $this->renderJSON($rows);
    }

    public function actionReporteCartera(){
      $this->template = null;
      return;
      $aSalida = array();
      $rows = Bitacora::model()->findAll("WHERE accion in ('Envió el prospecto a cartera.','Envió a cartera.')");
      foreach ($rows as $key => $value) {
        $rec = new Reclutador("WHERE user = ".$value->usuario);
        $model = new ProspectoCartera();
        $model->prospecto = $value->prospecto;
        $model->plazas = $rec->plazas;
        $model->reclutador_user = $value->usuario;
        $model->tipo_cartera = "micartera";
        $model->fecha_registro = date('Y-m-d', strtotime($value->created));
        if(!$model->save()){
          $aSalida["error"] .= $model->error;
        }
      }
      $aSalida["total"] = $rows;
      $this->renderJSON($aSalida);
    }


    public function actionReporteCarteraOld(){
      $this->template = null;
      return;
      $aSalida = array();
      $rows = Bitacora::model()->executeQuery("SELECT p.* FROM ec_prospecto p WHERE es_cartera = 1 AND cartera_tipo = 0 AND (SELECT COUNT(*) FROM ec_bitacora WHERE prospecto = p.id) = 0");
      foreach ($rows as $key => $value) {
        $bcartera = new ProspectoCartera();
        $bcartera->prospecto = $value->id;
        $bcartera->plazas = "-";
        $bcartera->tipo_cartera = "sistema";
        $bcartera->fecha_registro = date('Y-m-d', strtotime($value->created));
        if(!$bcartera->save()){
          $aSalida["error"] .= $model->error;
        }
      }
      $aSalida["total"] = $rows;
      $this->renderJSON($aSalida);
    }



    public function actionReporteCarteraGeneral(){
      $this->template = null;
      return;
      $aSalida = array();
      $rows = Bitacora::model()->findAll("WHERE accion in ('Envió el prospecto a cartera general.')");
      foreach ($rows as $key => $value) {
        $rec = new Reclutador("WHERE user = ".$value->usuario);
        $model = new ProspectoCartera();
        $model->prospecto = $value->prospecto;
        $model->plazas = $rec->plazas;
        $model->reclutador_user = $value->usuario;
        $model->tipo_cartera = "general";
        $model->fecha_registro = date('Y-m-d', strtotime($value->created));
        if(!$model->save()){
          $aSalida["error"] .= $model->error;
        }
      }
      $aSalida["total"] = $rows;
      $this->renderJSON($aSalida);
    }

    public function actionGetResumen(){
      $this->template = null;
      $this->render("resumen-qs", array("plazas" => $this->params["plazas"]));
    }

    public function actionModificaPaquetesSM(){
      $this->template = null;
      $aSalida = array();

      $rows = PublicidadPaquetePlaza::model()->findAll("WHERE fecha = '2022-08-01'");
      $modificados = 0;
      $aSalida["totalPaquetes"] = count($rows);

      foreach ($rows as $key => $value) {
        $plaza = new Plaza($value->plaza);
        $publicaciones = PublicidadPaquetePlazaPublicacion::model()->findAll("WHERE paquete_plaza = ".$value->id);
        foreach ($publicaciones as $skey => $svalue) {
          $amodel = new PublicidadPaquetePlazaPublicacion($svalue->id);
          $amodel->plaza = $plaza->nombre;
          $amodel->save();
          $modificados = $modificados + 1;
          // code...
        }
      }


      $aSalida["modificados"] = $modificados;

      $this->renderJSON($aSalida);
    }



    public function actionModificaPublicaciones(){
      $this->template = null;
      $aSalida = array();

      $rows = PublicidadPaquetePlazaPublicacion2::model()->findAll("WHERE id > 6521");
      $modificados = 0;
      $aSalida["totalPaquetes"] = count($rows);

      foreach ($rows as $key => $value) {
        $amodel = new PublicidadPaquetePlazaPublicacion("WHERE plaza LIKE '%".$value->plaza."%' AND nombre = '".$value->nombre."'");
        if($amodel->id != ""){
          $amodel->fecha = $value->fecha;
          $amodel->puesto = $value->puesto;
          $amodel->texto = $value->texto;
          $amodel->comentarios = $value->comentarios;
          $amodel->ubicaciones = $value->ubicaciones;
          $amodel->link = $value->link;
          $amodel->color = $value->color;
          $amodel->perfil = $value->perfil;
          $amodel->pauta = $value->pauta;
          $amodel->estatus = $value->estatus;
          $amodel->save();
          $modificados = $modificados + 1;
        }
      }


      $aSalida["modificados"] = $modificados;

      $this->renderJSON($aSalida);
    }

  }
?>