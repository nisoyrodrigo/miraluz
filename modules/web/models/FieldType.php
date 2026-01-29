<?php
	class FieldType extends Model{
		protected function init(){
			$this->tabla = "cms_field_type";
			$this->module = "cms";
		}
	}