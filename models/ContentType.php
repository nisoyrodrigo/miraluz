<?php
class ContentType extends Model{
	public function init(){
		$this->tabla = "cms_content_type";
		$this->module = "cms";
	}
}