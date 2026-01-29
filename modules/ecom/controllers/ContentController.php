<?php
	class ContentController extends Controller{
		public function actionInit(){
			$users = array();
			$types = array();
			$templates[] = array("value" => "0", "text" => "Sin plantilla");
			foreach(User::model()->findAll() as $user){
				$users[] = array("value" => $user->id, "text" => $user->username);
			}
			foreach(ContentType::model()->findAll() as $type){
				$types[] = array("value" => $type->id, "text" => $type->name);
			}
			foreach(Template::model()->findAll() as $template){
				$templates[] = array("value" => $template->id, "text" => $template->name);
			}
			$this->render("index", array(
				"users" => $users,
				"types" => $types,
				"templates" => $templates
			));
		}
		
		public function actionSelectType(){
			$types = ContentType::model()->findAll();
			$this->render("selecttype", array(
				"types" => $types
			));
		}
		
		public function actionEdit(){
			$content = new Content($this->params["id"]);
			if(empty($content->id)){
				$content->published = 1;
				$content->content_type = $this->params["type"];
			}
			$type = isset($this->params["type"]) ? $this->params["type"] : $content->content_type;
			$contentType = new ContentType($type);
			$fields = ContentTypeFieldType::model()->findAll("WHERE content_type = ".$type);
			$values = ContentValue::model()->findAll("WHERE content = ".$content->id);
			$fields_conf = array();
			foreach($fields as $field){
				$fields_conf[$field->id] = ContentFieldTypeConf::model()->findAll("WHERE content_type_field_type = ".$field->id)[0];
				$values[$field->id] = ContentValue::model()->findAll("WHERE content = ".$content->id." AND content_type_field_type = ".$field->id)[0];
			}
			
			$templates[] = array("value" => "0", "text" => "Sin plantilla");
			foreach(Template::model()->findAll() as $template){
				$templates[] = array("value" => $template->id, "text" => $template->name);
			}

			$this->template = null;
			
			$this->render("edit", array(
				"content" => $content,
				"contentType" => $contentType,
				"fields" => $fields,
				"fields_conf" => $fields_conf,
				"values" => $values,
				"templates" => $templates
			));
		}
		
		public function actionGetAll(){
			$content_type = $this->params["content_type"];
			$criteria = array(
				"condicion" => array("content_type" => $content_type)
			);
			$rows = Content::model()->findAll($criteria);
			for($i=0;$i<count($rows);$i++){
				if(empty($rows[$i]->template))
				{
					$rows[$i]->template = 0;
				}
			}
			$this->renderJSON($rows);
		}
		
		public function actionSave(){
			if($this->params["template"] == 0){
				$this->params["template"] = "";
			}
			$content = new Content($this->params["id"]);
			$content->setAttributes($this->params);
			if($content->save())
			{
				$fields = ContentTypeFieldType::model()->findAll("WHERE content_type = ".$content->content_type);
				foreach($fields as $field){
					$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type = ".$field->id);
					$value->content = $content->id;
					$value->content_type_field_type = $field->id;
					$value->value_field = $this->params["field_".$field->id];
					if(!$value->save()){
						$this->error .= $value->error."</br>"; 
					}
				}
			}else{
				$this->error = "Ocurrio un error al guardar el contenido: ".$content->error;
			}

			$this->renderJSON($content);
		}
		
		public function actionDelete(){
			$content = new Content($this->params["id"]);
			$this->error = $content->executeNonQuery("DELETE FROM cms_content_value WHERE content = ".$content->id);
			if(empty($this->error) && !$content->remove())
			{
				$this->error = $content->error;
			}
			$this->renderJSON();
		}
		
		public function actionSaveFile(){
			$id_content = $this->params["content"];
			$id_field = $this->params["id_field"];
			$field = new ContentTypeFieldType($id_field);
			
			$dir_subida = Motor::app()->absolute_url.$this->murl."/images/content";
			
			if(!file_exists($dir_subida)){
				mkdir($dir_subida, 0777);
			}

			$fichero_guardado = "images/content/".($id_field)."_".basename($_FILES['file_'.$id_field]["name"]);
			$fichero_subido = $dir_subida."/".($id_field)."_".basename($_FILES['file_'.$id_field]['name']);
						
			if (!move_uploaded_file($_FILES['file_'.$id_field]['tmp_name'], $fichero_subido)) {
				echo "error al guardar archivo en carpeta. ".$fichero_subido ;
			}
			else{
				
				$content = new Content($id_content);
				$content_value = new ContentValue("WHERE content = ".(empty($content->id)?"0":$content->id)." AND content_type_field_type = ".$id_field);
				$content_value->value_field = "http://".$this->burl.$this->murl."/".$fichero_guardado;
				$content_value->content_type_field_type = $field->id;
				if(empty($content->id)){
					$content->name = "temp";
					if(!$content->save()){
						$this->error = "error al guardar contenido: ".$content->error."\n";
					}
				}
				$content_value->content = $content->id;
				if(!$content_value->save()){
					$this->error .= "error al guardar valor ".$content_value->error;
				}
				
				$this->renderJSON($content_value);
			}
		}
	
		public function actionDeleteFile(){
			$id_content = $this->params["id_content"];
			$id_field = $this->params["id_field"];
			
			$content_value = new ContentValue("WHERE content = ".$id_content." AND content_type_field_type = ".$id_field);
			$aux = $content_value->value_field;
			$content_value->value_field = "";
			if(!$content_value->save()){
				$this->error = "error al guardar valor: ".$content->error."\n";
			}else{
				unlink($aux);
			}
			$this->renderJSON($content_value);
		}
	}