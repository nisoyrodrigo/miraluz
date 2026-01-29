<?php
	class Form{
		protected $attributes;
		protected $model;
		protected $field = array(
			"id"    => "",
			"name"  => "",
			"value" => "",
			"error"
		);
		protected $errors;
		
		abstract protected function init();
	}