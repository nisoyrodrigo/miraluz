<?php
	class TemplateController extends Controller{
		
		public function actionInit(){
			$this->render("index");
		}
		
		public function actionEdit(){
			$template = new Template($this->params["id"]);
			$this->render("edit", array("template" => $template));
		}
		
		public function actionGetAll(){
			$templates = Template::model()->findAll();
			$this->renderJSON($templates);
		}
		
		public function actionSave(){
			$template = new Template($this->params["id"]);
			$template->setAttributes($this->params);
			if(!$template->save()){
				$this->error = $template->error;
			}
			$this->renderJSON($template);
		}
		
		public function actionDelete(){
			$template = new Template($this->params["id"]);
			$listDataUsando = $template->executeQuery("
				SELECT 
					*
				FROM 
					cms_template t
				INNER JOIN 
					cms_list_data l ON t.id = l.template
				WHERE 
					t.id = ".$template->id
			);

			$contentUsando = $template->executeQuery("
				SELECT 
					*
				FROM 
					cms_template t
				INNER JOIN 
					cms_content c ON t.id = c.template
				WHERE 
					t.id = ".$template->id
			);
			
			if(count($listDataUsando) > 0){
				$this->error = "Hay ListData usando el template, ListData: ".$listDataUsando[0]->name;
			}
			else if (count($contentUsando) > 0){
				$this->error = "Hay Contenido usando el template, contenido: ".$contentUsando[0]->name;
			}
			else{
				if(!$template->remove()){
					$this->error = $template->error;
				}
			}
			
			$this->renderJSON();
		}
	}