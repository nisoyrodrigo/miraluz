<?php
	class DefaultController extends Controller{
		
		public function actionInit(){
			$persona = "alan";
			$this->render("index", array("persona" => $persona));
		}

		public function actionMensajeError(){
			echo "esto es un error";
		}

		public function actionQuery(){
			$error = "";
		}
		
		public function actionExecuteQuery(){
			echo User::model()->executeNonQuery("
				INSERT INTO cms_field_type VALUES(15, 'SpreadSheet');
			"
			);
			/*$block = new Block();
			echo "<pre>";
			print_r($block);
			echo "</pre>";exit();*/
		}

	}
?>