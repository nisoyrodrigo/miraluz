<?php
	class ListDataController extends Controller{
		public function actionInit(){
			foreach(User::model()->findAll() as $user){
				$users[] = array("value" => $user->id, "text" => $user->username);
			}
			$this->render("index", array("users" => $users));
		}

		public function actionGetAll(){
			$rows = ListData::model()->findAll();
			$this->renderJSON($rows);
		}

		public function actionEdit(){
			$content_types = array();
			$content_types[] = array("text" => "Ninguno", "value" => "0");
			foreach(ContentType::model()->findAll() as $content_type){
				$content_types[] = array("text" => $content_type->name, "value" => $content_type->id );
			}
			$model = new ListData($this->params["id"]);
			foreach(User::model()->findAll() as $user){
				$users[] = array("text" => $user->username, "value" => $user->id );
			}
			$model->query = str_replace('"', "'", $model->query);
			$this->render("edit", array("users" => $user, "model" => $model, "content_types" => $content_types));
		}

		public function actionSave(){
			$model = new ListData();
			$model->setAttributes($this->params);
			if(!$model->save()){
				$this->error = $model->error;
			}
			else{
				$block = new Block("WHERE list_data = ".$model->id);
				$block->name = empty($block->name) ? "List_Data : ".$model->name : $block->name;
				$block->list_data = $model->id;
				if(empty($block->id)){
					$resultado = $block->executeQuery("SELECT (max(order_block) + 1) as ordenamiento FROM cms_block");
					$block->order_block = empty($resultado[0]->ordenamiento) ? 1 : $resultado[0]->ordenamiento;
				}
				if(!$block->save()){
					$this->error = $block->error;
				}

			}
			$this->renderJSON($model);
		}

		public function actionDelete(){
			$model = new ListData($this->params["id"]);
			if(!$model->remove()){
				$this->error = $model->error;
			}
			$this->renderJSON();
		}
	}