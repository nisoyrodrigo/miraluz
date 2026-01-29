<?php
class ContentTypeFieldTypeController extends Controller{
	public function actionInit(){
		$contentType = new ContentType($this->params["CotentType"]);
		$fieldTypes[] = array("value" => "", "text"=> "Vacío");
		foreach(FieldType::model()->findAll("ORDER BY id") as $fieldType){
			$fieldTypes[] = array("text" => $fieldType->name, "value" => $fieldType->id);
		}
		$users[] = array("value" => "", "text" => "Vacío");
		foreach(User::model()->findAll() as $user){
			$users[] = array("value" => $user->id, "text" => $user->username);
		}
		$this->render("index", array("contentType" => $contentType, "fieldTypes" => $fieldTypes, "users" => $users));
	}

	public function actionEdit(){
		$field = new ContentTypeFieldType($this->params["id"]);
		$contentType = new ContentType($field->content_type);
		$fieldConf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
		$fieldTypes = FieldType::model()->findAll();
		
		$edit = array(
			1 => "texto",
			2 => "textolargo",
			3 => "numero",
			4 => "numero",
			6 => "opcionmultiple",
			7 => "archivo",
			8 => "imagen",
			9 => "fecha",
			10 => "fechayhora",
			11 => "verdaderofalso",
			12 => "email",
			13 => "video",
			14 => "query",
			15 => "hojacalculo"
		);
		
		//echo $field->field_type;exit();
		
		$this->render($edit[$field->field_type], array("contentType" => $contentType, "field" => $field, "fieldTypes" => $fieldTypes, "conf" => $fieldConf));
	}

	public function actionGetAll(){
		$fields = ContentTypeFieldType::model()->findAll("WHERE content_type = ".$this->params["ContentType"]." ORDER BY id ASC");
		$this->renderJSON($fields);
	}

	public function actionSave(){

		if(isset($this->params["field"])){
			$field = new ContentTypeFieldType($this->params["field"]["id"]);
			$field->setAttributes($this->params["field"]);
			if(!$field->save()){
				$this->error = $field->error;
			}

			if(isset($this->params["conf"])){
				$conf = new ContentFieldTypeConf($this->params["conf"]["id"]);
				$conf->setAttributes($this->params["conf"]);
				$conf->content_type_field_type = $field->id;
				if(!$conf->save()){
					$this->error .= " ".$conf->error;
				}
			}
		}
		else{
			$field = new ContentTypeFieldType($this->params["id"]);
			$field->setAttributes($this->params);
			$field_comp = new ContentTypeFieldType("WHERE name = '".$field->name."' AND id <> ".(empty($field->id) ? "0" : $field->id)." AND content_type = ".$contentType->id);
			if(empty($field_comp->id)){
				if(!$field->save()){
					$this->error = $field->error;
				}
			}
			else{
				$contentType = new ContentType($field->content_type);
				$this->error = "Ya existe un campo con el nombre ".$field->name." para ".$contentType->name;
			}
		}
		
		$this->renderJSON($field);
	}
	
	public function actionSaveFile(){
		$id_field = $this->params["id_field"];
		$id_conf = $this->params["id_conf"];
		
		
		$dir_subida = Motor::app()->absolute_url.$this->murl."/images/fields/";
		
		if(!file_exists($dir_subida)){
			mkdir($dir_subida, 0777);
		}

		$fichero_guardado = "images/fields/".($id)."_".basename($_FILES['default_value_file']["name"]);
		$fichero_subido = $dir_subida."/".($id)."_".basename($_FILES['default_value_file']['name']);
					
		if (!move_uploaded_file($_FILES['default_value_file']['tmp_name'], $fichero_subido)) {
			echo "error al guardar archivo en carpeta";
		}
		else{
			$conf = new ContentFieldTypeConf($id_conf);
			$conf->default_value = "http://".$this->burl.$this->murl."/".$fichero_guardado;
			$conf->content_type_field_type = $id_field;
			if(!$conf->save()){
				$this->error = "error al guardar archivo ".$conf->error;
			}
			$this->renderJSON($conf);
		}
	}
	
	public function actionDeleteFile(){
		$id_field = $this->params["id_field"];
		$id_conf = $this->params["id_conf"];
		
		$conf = new ContentFieldTypeConf($id_conf);
		$aux = $conf->default_value;
		$borrar = Motor::app()->absolute_url.str_replace("http://".$this->burl,"", $aux);
		$conf->default_value = "";
		if(!$conf->save()){
			$this->error = "error al guardar archivo ".$conf->error;
		}
		else{
			unlink($borrar);
		}
		$this->renderJSON($conf);
	}

	public function actionDelete(){
		$field = new ContentTypeFieldType($this->params["id"]);
		$values = ContentValue::model()->findAll("WHERE content_type_field_type = ".$field->id." AND value_field <> ''");
		if(count($values)==0){
			
			$this->error = ContentValue::model()->executeNonQuery("DELETE FROM cms_content_value WHERE content_type_field_type = ".$field->id);
			
			if($this->error == ""){
				$this->error = ContentFieldTypeConf::model()->executeNonQuery("DELETE FROM cms_content_field_type_conf WHERE 	content_type_field_type = ".$field->id);
			}
			
			if($this->error == ""){
				if(!$field->remove()){
					$this->error = $field->error;
				}
			}
		}
		else{
			$this->error = "Hay valores de contenido utilizando este campo";
		}
		$this->renderJSON();
	}

	public function actionQueryValidate(){
		$sql = $this->params["sql"];
		$label = $this->params["label"];
		$id_field = $this->params["field_id"];
		$id_conf = $this->params["conf_id"];
		$model =  new ContentFieldTypeConf($id_conf);

		if(strpos($sql, "DELETE") || strpos($sql, "UPDATE") || strpos($sql, "ALTER") || strpos($sql, "DROP") || strpos($sql, "TRUNCATE")){
			$this->error = "Solo se pueden utilizar querys de seleccion";
		}
		else{
			
			$model->content_type_field_type = $id_field;
			$model->query = addslashes($sql);
			$model->label = $label;
			if($model->save()){
				$model->executeQuery("SELECT COUNT(*) FROM ($sql) AS t1");
				if($model->error != ""){
					$this->error = "Error al validar query: <br/> $sql <br/> ".$model->error;
				}
			}
			else{
				$this->error = "Error al guardar la configuracion: ".$model->error;
			}
		}
		$this->renderJSON($model);
	}

	public function actionGetQuery(){
		$conf_id = $this->params["conf_id"];
		$model = new ContentFieldTypeConf($conf_id);

		$having = "";

		if(isset($this->params["filter"]["filters"]["0"]["value"])){
			$having = " HAVING text LIKE '%".$this->params["filter"]["filters"]["0"]["value"]."%'";
		}
		else if(!empty($model->default_value)){
			$default_value = $model->default_value;
			$having = " HAVING value IN ($default_value)";
		}

		$rows = $model->executeQuery($model->query.$having);
		$this->renderJSON($rows);
	}
}