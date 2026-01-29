<?php
	class ContentTypeController extends Controller{
		
		public function actionInit(){
			$users[] = array("value" => "", "text" => "Vacío");
			foreach(User::model()->findAll() as $user){
				$users[] = array("value" => $user->id, "text" => $user->username);
			}
			$templates[] = array("value" => "0", "text" => "Sin plantilla");
			foreach(Template::model()->findAll() as $template){
				$templates[] = array("value" => $template->id, "text" => $template->name);
			}
			$this->render("index", array("users" => $users, "templates" => $templates));
		}

		public function actionGetAll(){
			$rows = ContentType::model()->findAll();
			for($i=0;$i<count($rows);$i++){
				if(empty($rows[$i]->template))
				{
					$rows[$i]->template = 0;
				}
			}
			$this->renderJSON($rows);
		}

		public function actionSave(){
			if($this->params["template"] == 0)
			{
				$this->params["template"] = "";
			}
			$contentType = new ContentType($this->params["id"]);
			$contentType->setAttributes($this->params);
			if(!$contentType->save()){
				$this->error = $contentType->error;
			}
			if(empty($contentType->template)){
				$contentType->template = 0;
			}
			$this->renderJSON($contentType);
		}

		public function actionDelete(){
			$contentType = new ContentType($this->params["id"]);
			$contentType->remove();
			$this->renderJSON();
		}
	}