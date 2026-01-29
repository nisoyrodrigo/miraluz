<?php
  class NotificacionController extends Controller{
    
    public function actionInit(){
      $this->render("index");
    }

    public function actionReclutador(){
      $this->render("index-reclutadores");
    }

  }
?>