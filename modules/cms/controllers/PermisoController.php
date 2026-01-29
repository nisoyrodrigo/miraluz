<?php
	class PermisoController extends Controller{
		
		public function actionInit(){

			$sections = Section::model()->findAll();
			$users = User::model()->findAll();
			$rols = Rol::model()->findAll();
			$aSections = array();
			$aUsers = array();
			$aRols = array();
			foreach($sections as $section){
				$aSections[] = array("value" => $section->id, "text" => $section->name." - ".$section->action);
			}
			$aUsers[] = array("value" => "", "text" => "Vacío");
			foreach($users as $user){
				$aUsers[] = array("value" => $user->id, "text" => $user->username);
			}
			foreach($rols as $rol){
				$aRols[] = array("value" => $rol->id, "text" => $rol->name);
			}
			$this->render("index", array("sections" => $aSections, "users" => $aUsers, "rols" => $aRols));
		}

		public function actionGetAll(){
			$permisos = UserSection::model()->findAll();
			$this->renderJSON($permisos);
		}

		public function actionSave(){
			$permiso = new UserSection();
			$permiso->setAttributes($this->params);
			if(!$permiso->save()){
				$this->error = "error ".$permiso->error;
			}
			$this->renderJSON();
		}
		
		public function actionDelete(){
			$permiso = new UserSection($this->params["id"]); 
			if(!$permiso->remove()){
				$this->error = $permiso->error;
			}
			$this->renderJSON();
		}


	}