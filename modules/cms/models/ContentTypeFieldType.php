<?php
	class ContentTypeFieldType extends Model{
		protected function init(){
			$this->tabla = "cms_content_type_field_type";
			$this->module = "cms";
		}
	}