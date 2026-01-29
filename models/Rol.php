<?php
	class Rol extends Model{
		
		protected function init(){
			$this->tabla = "rol";
			$this->validaciones = array(
				"noNulo" => "name"
			);
		}
		
	}