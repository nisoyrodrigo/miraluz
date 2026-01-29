<?php
  class PublicidadPaquetePlazaController extends Controller{
    
    public function actionInit(){
      $this->render("index");
    }

    public function actionInitPublicaciones(){
      $this->render("index-publicaciones", array("id"=>$this->params["id"]));
    }

    public function actionReportePublicaciones(){
      $this->render("index-publicaciones-reporte");
    }

    public function actionCalendario(){
      $this->render("index-calendario");
    }

    public function actionMiCalendario(){
      $this->render("index-micalendario");
    }


    public function actionGetAll(){
      $this->template = null;
      $objReclutador = new Reclutador("WHERE user = ".$this->user->id);
      $auxWhere = "";
      if($this->user->id == 1){
    $auxWhere = " WHERE id > 748";
      } else {
        $auxWhere = " WHERE plaza IN (".$objReclutador->plazas.") AND id > 748";
      }
      $rows = PublicidadPaquetePlaza::model()->findAll($auxWhere);
      $tienePermiso = "0";
      if($this->revisaPermiso("PublicidadPaquetePlaza", "ModificaPaquetePlaza", $this->user->rol)){
        $tienePermiso = "1";
      }
      foreach ($rows as $key => $value) {
        $plaza = new Plaza($value->plaza);
        $paquete = new PublicidadPaquete($value->paquete);
        $reclutador = new Reclutador("WHERE user = ".$value->usuario);
        $rows[$key]->plaza_nombre = $plaza->nombre;
        $rows[$key]->paquete_nombre = $paquete->nombre;
        $rows[$key]->usuario_nombre = $reclutador->apaterno." ".$reclutador->amaterno." ".$reclutador->nombre;
        $rows[$key]->fecha = $this->getMes(date('n', strtotime($value->fecha)))." ".date('Y', strtotime($value->fecha));
        $rows[$key]->publicaciones = $paquete->publicaciones;
        $rows[$key]->tiene_permiso = $tienePermiso;
    $rows[$key]->creditos_restantes = $value->creditos. " de " .$value->creditos_total;
      }
      $this->renderJSON($rows);
    }

    public function actionGetAllPublicaciones(){
      $this->template = null;
      $tipos = array(
        "1" => "Sencilla",
        "2" => "Doble",
        "3" => "Triple",
      );
      $rows = PublicidadPaquetePlazaPublicacion::model()->findAll("WHERE paquete_plaza = ".$this->params["paquete_plaza"]);
      foreach ($rows as $key => $value) {
        $fecha = "Sin asignar";
        if($value->fecha != "0000-00-00"){
          $fecha = $value->fecha;
        }
        $rows[$key]->fecha = $fecha;
        $rows[$key]->tiponv = $tipos[$value->tiponv];
      }
      $this->renderJSON($rows);
    }

    public function actionGetAllPublicacionesReporte(){
      $this->template = null;
      $rows = PublicidadPaquetePlazaPublicacion::model()->findAll("WHERE fecha IS NOT NULL AND fecha != '0000-00-00'");
      foreach ($rows as $key => $value) {
        $pp = new PublicidadPaquetePlaza($value->paquete_plaza);
        $plaza = new Plaza($pp->plaza);
        $fecha = "Sin asignar";
        if($value->fecha != "0000-00-00"){
          $fecha = $value->fecha;
        }
        $rows[$key]->fecha = $fecha;
        $rows[$key]->plaza_nombre = $plaza->nombre;
      }
      $this->renderJSON($rows);
    }

    public function actionEditElement(){
      $this->template = null;
      $data = new PublicidadPaquetePlaza($this->params["id"]);
      $this->render("edit", array("data"=>$data));
    }

    public function actionEditPublicacion(){
      $this->template = null;
      $data = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $this->render("edit-publicacion", array("data"=>$data));
    }

    public function actionEditPublicacionNV(){
      $data = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $this->render("edit-publicacion-nv", array("data"=>$data));
    }

    public function actionEditPublicacionNV2(){
      $data = new PublicidadPaquetePlaza($this->params["id"]);
      $this->render("edit-publicacion-nv2", array("data"=>$data));
    }

    public function actionEditElementCalendario(){
      $this->template = null;
      $data = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $this->render("edit-publicacion-calendario", array("data"=>$data));
    }

    public function actionEditElementCalendarioNew(){
      $this->template = null;
      $data = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $this->render("edit-publicacion-calendario-new", array("data"=>$data));
    }


    public function actionSave(){
      $fecha = date('Y-m', strtotime("+1 month", time()))."-01";
	  $fecha = "2023-10-01";	
      $actual = new PublicidadPaquetePlaza("WHERE plaza = ".$this->params["plaza"]." AND fecha = '$fecha'");
      if($actual->id != ""){
        $this->error .= "Ya has solicitado un paquete para este mes.";
      }

      if($this->error == ""){
        $paquete = new PublicidadPaquete($this->params["paquete"]);
        $model = new PublicidadPaquetePlaza();
        $model->setAttributes($this->params);
        // $model->fecha = date('Y-m', strtotime("+1 month", time()))."-01";
        $model->fecha = $fecha;
        $model->creditos_total = $paquete->creditos;
        $model->creditos = $paquete->creditos;
        $model->usuario = $this->user->id;
        if(!$model->save()){
          $this->error = $model->error;
        } else {
          $dataModel = $model->getAttributes();
        }
      }
      
      if($this->error == ""){
        $paquete = new PublicidadPaquete($this->params["paquete"]);
        if($paquete->publicaciones > 0){
          for ($i=0; $i < $paquete->publicaciones; $i++) { 
            $nombre = "Publicación ".($i+1)." de ".$paquete->publicaciones;
            $amodel = new PublicidadPaquetePlazaPublicacion();
            $amodel->nombre = $nombre;
            $amodel->paquete_plaza = $dataModel->id;
            $amodel->fecha = "NULL";
            // $amodel->save(); 
          }
        }
      }

      $toRender = ($this->error != "") ? $model:$model->getAttributes();

      $this->renderJSON($toRender);
    }

    public function actionRenuevaPaquetes(){
     
      $rows = Plaza::model()->findAll("WHERE color IS NOT NULL");
      foreach ($rows as $key => $value) {
        $model = new PublicidadPaquetePlaza(); 
        $model->paquete = $value->color; 
        $model->plaza = $value->id; 
        $model->fecha = "2022-08-01"; 
        $model->usuario = "1";
        $model->estatus = 'pendiente';
        if(!$model->save()){
          $this->error = $model->error;
        } else {
          $dataModel = $model->getAttributes();
        }
        if($this->error == ""){
          $paquete = new PublicidadPaquete($value->color);
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

    public function actionDestroy(){
      $this->template = null;
      $model = new PublicidadPaquetePlaza($this->params["id"]);
      $model->estatus = 2;
      if(!$model->save()){
        $this->error .= "Error al eliminar registro.".$model->error;
      }
      $this->renderJSON($model->getAttributes());
    }


    public function actionSavePublicacion(){
      $model = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $model->setAttributes($this->params);
      $padre = new PublicidadPaquetePlaza($model->paquete_plaza);
      $plaza = new Plaza($padre->plaza);



      $previas = PublicidadPaquetePlazaPublicacion::model()->findAll("WHERE paquete_plaza = ".$padre->id);
      $nombre = "Publicación ".(count($previas) + 1);
      $model->nombre = $nombre;

      if($this->error == ""){
        $creditosAUsar = $this->params["tiponv"];
        $disponibles = $padre->creditos;
        if($creditosAUsar > $disponibles){
          $this->error .= "No tienes créditos disponibles para solicitar otra publicación.";
        }
      }

      if($this->error == ""){
        $diasAgregar = 0;
        switch ($this->params["tiponv"]) {
          case 1: // Tipo 1: 3 días
            $diasAgregar = 2;
            break;
          case 2: // Tipo 2: 5 días
            $diasAgregar = 4;
            break;
          case 3: // Tipo 3: 7 días
            $diasAgregar = 6;
            break;
          default:
            $diasAgregar = 2;
            break;
        }
        $fecha_inicio = $this->params["fecha"];
        $fecha_fin = date('Y-m-d', strtotime('+'.$diasAgregar.' days', strtotime($this->params["fecha"])));
        $model->fecha_fin = $fecha_fin;
        $problema = new PublicidadPaquetePlazaPublicacion("WHERE paquete_plaza = ".$this->params["paquete_plaza"]." AND (fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' OR fecha_fin BETWEEN '$fecha_inicio' AND '$fecha_fin')");
        
        if($problema->id != ""){
          $this->error .= "Lo lamento, tu publicación se empalma con ".$problema->nombre." que va del ".date('d/m/Y', strtotime($problema->fecha))." al ".date('d/m/Y', strtotime($problema->fecha_fin));
        }
        //$this->error .= "Fecha unucio ".$fecha_inicio." ->".$fecha_fin; 
      }

	  
		
	  if($this->error == ""){
	    $hoyFecha = date('Y-m-d', time());

      $hoyFecha = date('Y-m-d', strtotime('+2 days', strtotime($hoyFecha)));
  		if($this->params["fecha"] <= $hoyFecha){
  		  $this->error .= "No puedes solicitar Pautas para días anteriores a ".date('d/m/Y', strtotime($hoyFecha));
  		}
	  }

      if($this->error == ""){
        $model->estatus = 'solicitada';
      }

      if($this->error == ""){
        $restante = $padre->creditos - $this->params["tiponv"];
        $padre->creditos = strval($restante);
        if(!$padre->save()){
          $this->error = $padre->error." -> ".$this->params["tiponv"]."->".$padre->id." ( ".$padre->creditos;
        }
      }

      if($this->error == ""){
        if(!$model->save()){
          $this->error = $model->error;
        } else {
          $dataModel = $model->getAttributes();
        }  
      }
      
      $this->renderJSON($model->getAttributes());
    }

    public function actionSavePublicacionCalendario(){
      $model = new PublicidadPaquetePlazaPublicacion($this->params["id"]);
      $model->setAttributes($this->params);

      if($this->error == ""){
        $model->estatus = 'solicitada';
      }
		
	  if($this->error == "" && $this->params["link"] != ""){
        $model->estatus = 'publicado';
      }

      if($this->error == ""){
        if(!$model->save()){
          $this->error = $model->error;
        } else {
          $dataModel = $model->getAttributes();
        }  
      }
      
      $this->renderJSON($model->getAttributes());
    }

    public function actionApruebaPaquete(){
      $this->template = null;
      $model = new PublicidadPaquetePlaza($this->params["id"]);
      $model->estatus = "aprobado";
      if(!$model->save()){
        $this->error .= "Error al aprobar paquete.";
      }
      $this->renderJSON($model->getAttributes());
    }

    public function actionCancelaPaquete(){
      $this->template = null;
      $model = new PublicidadPaquetePlaza($this->params["id"]);
      $model->estatus = "cancelado";
      if(!$model->save()){
        $this->error .= "Error al cancelar paquete.";
      }
      $this->renderJSON($model->getAttributes());
    }

    public function actionGetTextos(){
      $this->template = null;
      $model = new PublicacionPerfil($this->params["tipo"]);
      $textos = PublicacionPerfilTexto::model()->findAll("WHERE tipo = ".$model->tipo);
      $sHtml = '';
      foreach ($textos as $key => $value) {
        $sHtml .= '<option value="'.$value->id.'">'.str_replace("\n", "<br>", $value->texto).'</option>';
      }
      if($this->params["tipo"] == ""){
        $sHtml = "";
      }
      $this->renderJSON($sHtml);
    }



    public function actionGetPaquetes(){
      $this->template = null;
      $plaza = new Plaza($this->params["plaza"]);
      $tipo = ($plaza->es_cedis == "1") ? "2":"3";
      $paquetes = PublicidadPaquete::model()->findAll("WHERE tipo = $tipo");
      $sHtml = '<option value="">Selecciona un paquete...</option>';
      //$this->error = $this->params;
      foreach ($paquetes as $key => $value) {
        //$checked = (in_array($value->id, $municipiosPrevios)) ? "selected":"";
        $checked = "";
        $sHtml .= '<option value="'.$value->id.'" '.$checked.'>'.$value->nombre.'</option>';
      }
      $this->renderJSON($sHtml);
    }

    public function actionPreviaPublicacion(){
      $this->template = null;
      $this->render("previa-publicacion", array("perfil"=>$this->params["perfil"], "diseno"=>$this->params["diseno"], "color"=>$this->params["color"]));
    }

    public function actionGetColores(){
      $this->template = null;
      $rows = PublicidadPaqueteColor::model()->findAll("WHERE diseno = ".$this->params["diseno"]);
      $sHtml = '<option value="">Selecciona una opción...</option>';
      //$this->error = $this->params;
      foreach ($rows as $key => $value) {
        //$checked = (in_array($value->id, $municipiosPrevios)) ? "selected":"";
        $checked = "";
        $sHtml .= '<option value="'.$value->id.'" '.$checked.'>'.$value->nombre.'</option>';
      }
      $this->renderJSON($sHtml);
    }

    public function actionGetPerfiles(){
      $this->template = null;
      $rows = PublicidadPaquetePerfil::model()->findAll("WHERE diseno = ".$this->params["diseno"]);
      $sHtml = '<option value="">Selecciona una opción...</option>';
      //$this->error = $this->params;
      foreach ($rows as $key => $value) {
        //$checked = (in_array($value->id, $municipiosPrevios)) ? "selected":"";
        $checked = "";
        $sHtml .= '<option value="'.$value->id.'" '.$checked.'>'.$value->nombre.'</option>';
      }
      $this->renderJSON($sHtml);
    }

  }
?>