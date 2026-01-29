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
				$verificaContent = new Content("WHERE name = 'temp' AND user = ".$this->user->id);
				if(!empty($verificaContent->id)){
					$verificaContent->executeNonQuery("DELETE FROM cms_content_value WHERE content = ".$verificaContent->id);
					$verificaContent->remove();
				}
				$content->published = 1;
				$content->content_type = $this->params["type"];
			}
			$type = isset($this->params["type"]) ? $this->params["type"] : $content->content_type;
			$contentType = new ContentType($type);		
			$fields = ContentTypeFieldType::model()->findAll("WHERE content_type = ".$type);
			
			$templates[] = array("value" => "0", "text" => "Sin plantilla");
			foreach(Template::model()->findAll() as $template){
				$templates[] = array("value" => $template->id, "text" => $template->name);
			}
			
			$this->render("edit", array(
				"content" => $content,
				"contentType" => $contentType,
				"fields" => $fields,
				"templates" => $templates
			));
		}
		
		public function actionGetAll(){
			$rows = Content::model()->findAll("WHERE name <> 'temp'");
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
				/*echo "<pre>";
				print_r($fields);
				echo "</pre>";
				echo "<pre>";
				print_r($this->params);
				echo "</pre>";exit();*/
				foreach($fields as $field){
					if(is_array($this->params["field_".$field->id])){
						foreach($this->params["field_".$field->id] as $fieldOrder => $fieldValue){
							$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type = ".$field->id. " AND order_value = ".$fieldOrder);
							$value->content = $content->id;
							$value->content_type_field_type = $field->id;
							$value->value_field = $fieldValue;
							$value->order_value = $fieldOrder;
							if(!$value->save()){
								$this->error .= $value->error."</br>"; 
							}
						}
					}
					else{
						if(isset($this->params["field_".$field->id])){
							$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type = ".$field->id);
							$value->content = $content->id;
							$value->content_type_field_type = $field->id;
							$value->value_field = $this->params["field_".$field->id];
							
							if(!$value->save()){
								$this->error .= $value->error."</br>"; 
							}
						}
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
			$id_value = 0;
			if(isset($this->params["id"])){
				$id_value = $this->params["id"];
				$content_value = new ContentValue($id_value);
				$id_content = $content_value->content;
				$id_field[0] = $content_value->content_type_field_type;
				$id_field[1] = $content_value->order_value;
				$field = new ContentTypeFieldType($id_field[0]);	
			}
			else{
				$id_content = $this->params["content"];
				$id_field = explode("_", $this->params["id_field"]);
				if(count($id_field)==1){
					$sql = "
						SELECT max(order_value) maximo FROM cms_content_value WHERE content = $id_content AND content_type_field_type = $id_field[0]
					";
					$id_field[1] = ContentValue::model()->executeQuery($sql)[0]->maximo + 1;
				}
				$field = new ContentTypeFieldType($id_field[0]);
			}
			
			$dir_subida = Motor::app()->absolute_url.$this->murl."/images/content";
			
			if(!file_exists($dir_subida)){
				mkdir($dir_subida, 0777);
			}

			$fichero_guardado = "images/content/".($this->params["id_field"])."_".basename($_FILES['file_'.$this->params["id_field"]]["name"]);
			$fichero_subido = $dir_subida."/".($this->params["id_field"])."_".basename($_FILES['file_'.$this->params["id_field"]]['name']);
						
			if (!move_uploaded_file($_FILES['file_'.$this->params["id_field"]]['tmp_name'], $fichero_subido)) {
				echo "error al guardar archivo en carpeta. ".$fichero_subido ;
			}
			else{
				if(empty($id_content)){
					$content = new Content("WHERE name = 'temp' AND user = ".$this->user->id);
				}
				else{
					$content = new Content($id_content);
				}
				$content_value = new ContentValue("WHERE content = ".(empty($content->id)?"0":$content->id)." AND content_type_field_type = ".$id_field[0]." AND order_value = ".$id_field[1]);
				$content_value->value_field = $this->murl."/".$fichero_guardado;
				$content_value->content_type_field_type = $id_field[0];
				$content_value->order_value = $id_field[1];
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
			if(isset($this->params["id"])){
				$content_value = new ContentValue($this->params["id"]);
			}
			else{
				$id_content = $this->params["content"];
				$id_field = $this->params["id_field"];
				$content_value = new ContentValue("WHERE content = ".$id_content." AND content_type_field_type = ".$id_field);
			}
			$aux = Motor::app()->absolute_url.$content_value->value_field;
			$content_value->value_field = "";
			if(!$content_value->remove()){
				$this->error = "error al borrar valor: ".$content_value->error."\n";
			}else{
				unlink($aux);
			}
			
			$this->renderJSON();
		}

		public function actionGetFieldQuery(){
			$conf_id = $this->params["conf_id"];
			$model = new ContentFieldTypeConf($conf_id);

			$having = "";

			if(isset($this->params["filter"]["filters"]["0"]["value"])){
				$having = " HAVING text LIKE '%".$this->params["filter"]["filters"]["0"]["value"]."%'";
			}
			else if(!empty($this->params["valor"])){
				$valor = $this->params["valor"];
				$having = " HAVING value IN ($valor)";
			}
			else if(!empty($model->default_value)){
				$default_value = substr($model->default_value, 1, strlen($model->default_value));
				$having = " HAVING value IN ($default_value)";
			}

			$rows = $model->executeQuery($model->query.$having);
			$this->renderJSON($rows);
		}
		
		public function actionGetValueForField(){
			$id_field = $this->params["field"];
			$id_content = $this->params["content"];
			$values = ContentValue::model()->findAll("WHERE content = ".$id_content." AND content_type_field_type = ".$id_field);
			$this->renderJSON($values);
		}
		
		public function actionAddValueField(){
			$id_field = $this->params["id_field"];
			$id_content = $this->params["content"];
			$value = $this->params["value"];
			if(is_array($value)){
				$value = json_encode($value);
			}

			$content = new Content($id_content);
			
			if(empty($id_content)){
				$order_value = 1;
			}
			else{
				$sql = "
					SELECT max(order_value) maximo FROM cms_content_value WHERE content = $id_content AND content_type_field_type = $id_field
				";
				$order_value = ContentValue::model()->executeQuery($sql)[0]->maximo + 1;
			}
			
			if(empty($content->id)){
				$verificaContent = new Content("WHERE name = 'temp' AND user = ".$this->user->id);
				if(!empty($verificaContent->id)){
					$verificaContent->executeNonQuery("DELETE FROM cms_content_value WHERE content = ".$verificaContent->id);
					$verificaContent->remove();
				}
				$content->name = "temp";
				if(!$content->save()){
					$this->error = "Error al guardar Contenido temporal: ".$content->error;
				}
			}
			
			$contentValue = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type = ".$id_field." AND order_value = ".$order_value);
			
			if(empty($this->error)){
				
				$contentValue->content = $content->id;
				$contentValue->content_type_field_type = $id_field;
				$contentValue->value_field = $value;
				$contentValue->order_value = $order_value;
				if(!$contentValue->save()){
					$this->error = "Ocurrio un error al guardar el valor: ".$contentValue->error;
				}
			}
			$this->renderJSON($contentValue);
		}
		
		public function actionDeleteValueField(){
			$content_value = new ContentValue($this->params["id"]);
			if(!$content_value->remove()){
				$this->error = "error al borrar valor: ".$content_value->error."\n";
			}
			$this->renderJSON();
		}
	}