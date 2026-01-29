<?php
  class CorteController extends Controller{
    
    public function actionInit(){
      $this->render("index");
    }

    public function actionCorteDia(){
      $plazas = Plaza::model()->findAll("WHERE id < 54 AND id BETWEEN 1 AND 28");
      $fecha = date('Y-m-d',strtotime("-1 days"));
      //$fecha = "2021-05-30";
      $rows = array();

      foreach ($plazas as $key => $value) {
        $model = new PlazaCorte("WHERE plaza = ".$value->id." AND fecha = '$fecha'");
        $model->plaza = $value->id;
        $model->fecha = $fecha;
        //Sin asignar
        $sin_asignar = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus = '0' AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0";
        $model->sin_asignar = Prospecto::model()->executeQuery($sin_asignar)[0]->total;
        $sin_asignar_dia = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus = '0' AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0 AND DATE_FORMAT(pp.created, '%Y-%m-%d') = '$fecha'";
        $model->sin_asignar_dia = Prospecto::model()->executeQuery($sin_asignar_dia)[0]->total;
        //Sin contactar
        $sin_contactar = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND estatus IN (1,2,4) AND es_cartera = 0";
        $model->sin_contactar = Prospecto::model()->executeQuery($sin_contactar)[0]->total;
        $sin_contactar_dia = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus IN (1,2,4) AND p.es_cartera = 0 AND DATE_FORMAT(pp.created, '%Y-%m-%d') = '$fecha'";
        $model->sin_contactar_dia = Prospecto::model()->executeQuery($sin_contactar_dia)[0]->total;
        //En cartera
        $cartera_general = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 0 AND p.es_cartera = 1";
        $model->cartera_general = Prospecto::model()->executeQuery($cartera_general)[0]->total;
        $cartera_general_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto_cartera p WHERE p.plazas = ".$value->id." AND p.tipo_cartera = 'general' AND DATE_FORMAT(p.fecha_registro, '%Y-%m-%d') = '$fecha'";
        $model->cartera_general_dia = Prospecto::model()->executeQuery($cartera_general_dia)[0]->total;
        
        $mi_cartera = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 1 AND p.es_cartera = 1";
        $model->mi_cartera = Prospecto::model()->executeQuery($mi_cartera)[0]->total;
        $mi_cartera_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.accion = 'Envió el prospecto a cartera.' AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->mi_cartera_dia = Prospecto::model()->executeQuery($mi_cartera_dia)[0]->total;

        //En proceso
        $proceso = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (3,5,6,8,10) AND p.es_cartera = 0";
        $model->proceso = Prospecto::model()->executeQuery($proceso)[0]->total;
        $proceso_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(3,5,6,8,10) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->proceso_dia = Prospecto::model()->executeQuery($proceso_dia)[0]->total;
        //contratado
        $contratado = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto_contratacion p WHERE p.plaza = ".$value->id."";
        $model->contratado = Prospecto::model()->executeQuery($contratado)[0]->total;
        $contratado_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(11) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->contratado_dia = Prospecto::model()->executeQuery($contratado_dia)[0]->total;
        //descartado
        $descartado = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN(7,9,12,13) AND p.es_cartera = 0";
        $model->descartado = Prospecto::model()->executeQuery($descartado)[0]->total;
        $descartado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(7,9,12,13) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->descartado_dia = Prospecto::model()->executeQuery($descartado_dia)[0]->total;


        //$this->error = $sin_contactar;
        if(!$model->save()){
          $this->error = "Error al guardar".$model->error;
        }

        $aux = array();
        $aux["id"] = $value->id;
        $aux["nombre"] = $value->nombre;
        $aux["data"] = $model->sin_asignar;
        $rows[] = $aux;
      }

      $this->renderJSON();

    }

    public function actionCorteDia2(){
      $plazas = Plaza::model()->findAll("WHERE id < 54 AND id BETWEEN 27 AND 55");
      $fecha = date('Y-m-d',strtotime("-1 days"));
      //$fecha = "2021-05-30";
      $rows = array();

      foreach ($plazas as $key => $value) {
        $model = new PlazaCorte("WHERE plaza = ".$value->id." AND fecha = '$fecha'");
        $model->plaza = $value->id;
        $model->fecha = $fecha;
        //Sin asignar
        $sin_asignar = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus = '0' AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0";
        $model->sin_asignar = Prospecto::model()->executeQuery($sin_asignar)[0]->total;
        $sin_asignar_dia = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus = '0' AND (p.reclutador_user IS NULL OR p.reclutador_user = '') AND p.es_cartera = 0 AND DATE_FORMAT(pp.created, '%Y-%m-%d') = '$fecha'";
        $model->sin_asignar_dia = Prospecto::model()->executeQuery($sin_asignar_dia)[0]->total;
        //Sin contactar
        $sin_contactar = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND estatus IN (1,2,4) AND es_cartera = 0";
        $model->sin_contactar = Prospecto::model()->executeQuery($sin_contactar)[0]->total;
        $sin_contactar_dia = " SELECT pp.id, pp.prospecto, pp.plaza, IFNULL(COUNT(*),0) AS total FROM ec_prospecto_plaza pp LEFT JOIN ec_prospecto p ON pp.prospecto = p.id WHERE pp.plaza = ".$value->id." AND p.estatus IN (1,2,4) AND p.es_cartera = 0 AND DATE_FORMAT(pp.created, '%Y-%m-%d') = '$fecha'";
        $model->sin_contactar_dia = Prospecto::model()->executeQuery($sin_contactar_dia)[0]->total;
        //En cartera
        $cartera_general = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 0 AND p.es_cartera = 1";
        $model->cartera_general = Prospecto::model()->executeQuery($cartera_general)[0]->total;
        $cartera_general_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto_cartera p WHERE p.plazas = ".$value->id." AND p.tipo_cartera = 'general' AND DATE_FORMAT(p.fecha_registro, '%Y-%m-%d') = '$fecha'";
        $model->cartera_general_dia = Prospecto::model()->executeQuery($cartera_general_dia)[0]->total;
        $mi_cartera = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 1 AND p.es_cartera = 1";
        $model->mi_cartera = Prospecto::model()->executeQuery($mi_cartera)[0]->total;
        $mi_cartera_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.accion = 'Envió el prospecto a cartera.' AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->mi_cartera_dia = Prospecto::model()->executeQuery($mi_cartera_dia)[0]->total;

        //En proceso
        $proceso = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (3,5,6,8,10) AND p.es_cartera = 0";
        $model->proceso = Prospecto::model()->executeQuery($proceso)[0]->total;
        $proceso_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(3,5,6,8,10) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->proceso_dia = Prospecto::model()->executeQuery($proceso_dia)[0]->total;
        //contratado
        $contratado = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_prospecto_contratacion p WHERE p.plaza = ".$value->id."";
        $model->contratado = Prospecto::model()->executeQuery($contratado)[0]->total;
        $contratado_dia = " SELECT IFNULL(COUNT(*),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(11) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->contratado_dia = Prospecto::model()->executeQuery($contratado_dia)[0]->total;
        //descartado
        $descartado = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN(7,9,12,13) AND p.es_cartera = 0";
        $model->descartado = Prospecto::model()->executeQuery($descartado)[0]->total;
        $descartado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(7,9,12,13) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'";
        $model->descartado_dia = Prospecto::model()->executeQuery($descartado_dia)[0]->total;


        //$this->error = $sin_contactar;
        if(!$model->save()){
          $this->error = "Error al guardar".$model->error;
        }

        $aux = array();
        $aux["id"] = $value->id;
        $aux["nombre"] = $value->nombre;
        $aux["data"] = $model->sin_asignar;
        $rows[] = $aux;
      }

      $this->renderJSON();

    }


    public function actionCorteReclutadorDia(){
      set_time_limit(99000);
      $fecha = date('Y-m-d',strtotime("-1 days"));
      $ultimoreclu = ReclutadorCorte::model()->executeQuery("SELECT IFNULL(reclutador_user, 1) AS reclutador FROM ec_reclutador_corte WHERE fecha = '$fecha' ORDER BY reclutador_user DESC LIMIT 1")[0]->reclutador;
      if($ultimoreclu == "") $ultimoreclu = "1";
      $reclutadores = Reclutador::model()->executeQuery("SELECT id, user, plazas FROM ec_reclutador WHERE estatus = 1 AND user BETWEEN $ultimoreclu AND 5950");
      //$this->error = "SELECT id, user, plazas FROM ec_reclutador WHERE estatus = 1 AND id BETWEEN $ultimoreclu AND 5950";
      $rows = array();
      if($this->error == ""){
        foreach ($reclutadores as $skey => $svalue) {
          $plazas = Plaza::model()->findAll("WHERE id IN (".$svalue->plazas.")");
          foreach ($plazas as $key => $value) {
            $model = new ReclutadorCorte("WHERE plaza = ".$value->id." AND fecha = '$fecha' AND reclutador_user = ".$svalue->user."");
            $model->plaza = $value->id;
            $model->fecha = $fecha;
            $model->reclutador_user = $svalue->user;
           
            //Sin contactar

            $sin_contactar = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND estatus IN (1,2,4) AND es_cartera = 0 AND reclutador_user = ".$svalue->user."";
            $model->sin_contactar = Prospecto::model()->executeQuery($sin_contactar)[0]->total;
            //En cartera
            $mi_cartera = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 1 AND p.es_cartera = 1 AND p.reclutador_user = ".$svalue->user." ";
            $model->mi_cartera = Prospecto::model()->executeQuery($mi_cartera)[0]->total;
            $mi_cartera_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.accion = 'Envió el prospecto a cartera.' AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'  ";
            $model->mi_cartera_dia = Prospecto::model()->executeQuery($mi_cartera_dia)[0]->total;
            

            $proceso = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (3,5,6,8,10) AND p.es_cartera = 0 AND p.reclutador_user = ".$svalue->user." ";
            $model->proceso = Prospecto::model()->executeQuery($proceso)[0]->total;
            $proceso_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(3,5,6,8,10) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
            $model->proceso_dia = Prospecto::model()->executeQuery($proceso_dia)[0]->total;
         
            //contratado
            $contratado = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_prospecto_contratacion p WHERE p.plaza = ".$value->id." AND p.reclutador_user = ".$svalue->user."";
            $model->contratado = Prospecto::model()->executeQuery($contratado)[0]->total;
            $contratado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(11) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
            //$this->error = $contratado_dia;
            $model->contratado_dia = Prospecto::model()->executeQuery($contratado_dia)[0]->total;
             //descartado
            $descartado = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (7,9,12,13) AND p.es_cartera = 0 AND p.reclutador_user = ".$svalue->user." ";
            $model->descartado_total = Prospecto::model()->executeQuery($descartado)[0]->total;
            $descartado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(7,9,12,13) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
            $model->descartado_total_dia = Prospecto::model()->executeQuery($descartado_dia)[0]->total;


            //$this->error = $sin_contactar;
            if(!$model->save()){
              $this->error = "Error al guardar".$model->error;
            }

            $aux = array();
            $aux["id"] = $value->id;
            $aux["nombre"] = $value->nombre;
            $aux["data"] = $model->sin_asignar;
            $rows[] = $aux;
          }

        }
      }
      $this->renderJSON($rows);

    }

    public function actionCorteReclutadorDiaRepara(){
      $reclutadores = Reclutador::model()->executeQuery("SELECT id, user, plazas FROM ec_reclutador WHERE estatus = 1 AND id BETWEEN 1 AND 1500");
      $fecha = date('Y-m-d',strtotime("-1 days"));
      $rows = array();
      if($this->error == ""){
        foreach ($reclutadores as $skey => $svalue) {
          $plazas = Plaza::model()->findAll("WHERE id IN (".$svalue->plazas.")");
          foreach ($plazas as $key => $value) {
            $model = new ReclutadorCorte("WHERE plaza = ".$value->id." AND fecha = '$fecha' AND reclutador_user = ".$svalue->user."");
            if($model->id != ""){

              //En cartera
              $mi_cartera = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus = '0' AND p.cartera_tipo = 1 AND p.es_cartera = 1 AND p.reclutador_user = ".$svalue->user." ";
              $model->mi_cartera = Prospecto::model()->executeQuery($mi_cartera)[0]->total;
              $mi_cartera_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.accion = 'Envió el prospecto a cartera.' AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha'  ";
              $model->mi_cartera_dia = Prospecto::model()->executeQuery($mi_cartera_dia)[0]->total;
              

              $proceso = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (3,5,6,8,10) AND p.es_cartera = 0 AND p.reclutador_user = ".$svalue->user." ";
              $model->proceso = Prospecto::model()->executeQuery($proceso)[0]->total;
              $proceso_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(3,5,6,8,10) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
              $model->proceso_dia = Prospecto::model()->executeQuery($proceso_dia)[0]->total;
           
              //contratado
              $contratado = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_prospecto_contratacion p WHERE p.plaza = ".$value->id." AND p.reclutador_user = ".$svalue->user."";
              $model->contratado = Prospecto::model()->executeQuery($contratado)[0]->total;
              $contratado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(11) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
              //$this->error = $contratado_dia;
              $model->contratado_dia = Prospecto::model()->executeQuery($contratado_dia)[0]->total;
               //descartado
              $descartado = " SELECT IFNULL(COUNT(DISTINCT(p.id)),0) AS total FROM ec_prospecto p WHERE p.plaza_asignada = ".$value->id." AND p.estatus IN (7,9,12,13) AND p.es_cartera = 0 AND p.reclutador_user = ".$svalue->user." ";
              $model->descartado_total = Prospecto::model()->executeQuery($descartado)[0]->total;
              $descartado_dia = " SELECT IFNULL(COUNT(DISTINCT(p.prospecto)),0) AS total FROM ec_bitacora p WHERE p.plaza = ".$value->id." AND p.estatus IN(7,9,12,13) AND DATE_FORMAT(p.created, '%Y-%m-%d') = '$fecha' AND p.usuario = ".$svalue->user."  ";
              $model->descartado_total_dia = Prospecto::model()->executeQuery($descartado_dia)[0]->total;


              //$this->error = $sin_contactar;
              if(!$model->save()){
                $this->error = "Error al guardar".$model->error;
              }

            }
            $aux = array();
            $aux["id"] = $value->id;
            $aux["nombre"] = $value->nombre;
            $aux["data"] = $model->sin_asignar;
            $rows[] = $aux;
          }

        }
      }
      $this->renderJSON($rows);

    }

  }
?>