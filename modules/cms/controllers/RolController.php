<?php
	class RolController extends Controller{
		
		public function actionInit(){
			$rol = new Rol();
			$this->render("index");
		}
		
		public function actionSave(){
			$rol = new Rol();
			$rol->setAttributes($_POST);
			if(!$rol->save()){
				$this->error = $rol->error;
			}
			$this->renderJSON();
		}
		
		public function actionGetAll(){
			$this->renderJSON(Rol::model()->findAll());
		}
		
		public function actionDelete(){
			$rol = new Rol($this->params["id"]);
			
			$usuario = new User("WHERE rol = ".$rol->id);
			if(!empty($usuario->id)){
				$this->error = "El rol no se puede eliminar porque hay usuarios utilizandolo";
			}
			else if(!$rol->remove()){
				$this->error = $rol->error;
			}
			
			$this->renderJSON();
		}
	}