<?php
	class Content extends Model{
		public function init(){
			$this->tabla = "cms_content";
			$this->module = "cms";
		}
	}