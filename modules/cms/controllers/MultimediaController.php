<?php
	class MultimediaController extends Controller{
		public function actionInit(){
			$this->template = "multimedia";
			$types = array();
			foreach(TypeMultimedia::model()->findAll() as $type){
				$types[] = array("text" => $type->name, "value" => $type->id);
			}
			$this->render("index", array("types" => $types));
		}

		public function actionGetAll(){
			$rows = Multimedia::model()->findAll();
			$this->renderJSON($rows);
		}

		public function actionSave(){
			$id = empty($this->params["id"]) ? $this->params["id_aux"] : $this->params["id"];
			unset($this->params["id"]);
			$multimedia = new Multimedia($id);
			$multimedia->setAttributes($this->params);
			if(!$multimedia->save()){
				$this->error = $multimedia->error;
			}
			$this->renderJSON($multimedia);
		}

		public function actionSaveFile(){

			if(!empty($this->params["id"])){
				$id = $this->params["id"];
			}
			else{
				$id = Multimedia::model()->executeQuery("SELECT (MAX(id) + 1) as id FROM cms_multimedia")[0]->id;

			}
			
			$dir_subida = Motor::app()->absolute_url.$this->murl."/multimedia";
			
			if(!file_exists($dir_subida)){
				mkdir($dir_subida, 0777);
			}

			$fichero_guardado = "multimedia/".($id)."_".basename($_FILES['archivo']["name"]);
			$fichero_subido = $dir_subida."/".($id)."_".basename($_FILES['archivo']['name']);
						
			if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $fichero_subido)) {
				echo "error al guardar archivo en carpeta";
			}
			else{
				$multimedia = new Multimedia();
				$multimedia->url = "http://".$this->burl.$this->murl."/".$fichero_guardado;
				$multimedia->name = "temp";
				if(!$multimedia->save()){
					echo "error al guardar archivo";
				}
				$this->renderJSON($multimedia);
			}
		}

		public function actionDelete(){
			$multimedia = new Multimedia($this->params["id"]);
			if(!$multimedia->remove()){
				$this->error = $multimedia->error;
			}
			$this->renderJSON();
		}
	}
