<?php
	class AuthController extends Controller{

		public function actionLogin(){
			
			$username = isset($this->params["username"]) ? $this->params["username"] : null;
			$password = isset($this->params["password"]) ? $this->params["password"] : null;
			
			if(!empty($username) && !empty($password))
			{
				$user = new User("WHERE username = '".$username."' AND password = SHA2('".$password."', 256) AND status = 'Active' AND rol <> 2");
				
				if($user->id != 0){
					$usuario = new stdClass();
					$usuario->id = $user->id;
					$usuario->username = $user->username;
					$usuario->rol = $user->rol;
					Motor::app()->setUser($this->module, $usuario);
					$this->redirect("cms");
				}
				else{
					$this->render("login", array(
						"username" => $username, 
						"password" => $password,
						"error" => "Usuario o password incorrectos"
					));
				}
			}
			else{
				$this->render("login", array(
					"username" => $username, 
					"password" => $password,
					"error" => ""
				));
			}
		}

		public function actionLogOut(){
			Motor::app()->userLogout($this->module);
			$this->redirect("cms/auth/login");
		}
		
	}