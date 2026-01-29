<?php
	class UserController extends Controller{
		
		public function actionInit(){

			$roles = Rol::model()->findAll();
			$aRoles = array();
			foreach($roles as $rol){
				$aRoles[] = array("value" => $rol->id, "text" => $rol->name);
			}

			$this->render("index", array("roles" => $aRoles));
		}

		public function actionGetAll(){
			$usuarios = User::model()->findAll();
			$this->renderJSON($usuarios);
		}

		public function actionSave(){
			$usuario = new User();
			$usuario->setAttributes($this->params);
			if(!$usuario->save()){
				$this->error = $usuario->error;
			}
			$this->renderJSON();
		}
		
		public function actionDestroy(){
			$user = new User($this->params["id"]);
			
			$maestro = new Maestro("WHERE maestro_user = ".$user->id);
			$alumno = new Alumno("WHERE alumno_user = ".$user->id);
			if(!empty($maestro->id)){
				$this->error = "El usuario no se puede eliminar porque ya pertenece al maestro ".$maestro->nombre;
			}
			else if(!empty($alumno->id))
			{
				$this->error = "El usuario no se puede eliminar porque ya pertenece al alumno ".$alumno->nombre;
			}
			else if(!$user->remove()){
				$this->error = $user->error;
			}
			
			$this->renderJSON();
		}


	}