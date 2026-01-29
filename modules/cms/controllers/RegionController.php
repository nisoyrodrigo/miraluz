<?php
	class RegionController extends Controller{
		public function actionInit(){
			$this->render("index");
		}
		
		public function actionGetAll(){
			$regiones = Region::model()->findAll();
			$this->renderJSON($regiones);
		}
		
		public function actionSave(){
			$region = new Region();
			$region->setAttributes($this->params);
			if(!$region->save()){
				$this->error = $region->error;
			}
			$this->renderJSON($region);
		}
		
		public function actionDelete(){
			$region = new Region($this->params["id"]);
			$block = new Block("WHERE region = ".$region->id);
			
			if(!empty($block->id)){
				$this->error = "La region no puede ser eliminada ya que esta siendo utilizada por el Bloque ".$block->nombre;
			}
			else if(!$region->remove()){
				$this->error = $region->error;
			}
			
			$this->renderJSON();
		}
	}