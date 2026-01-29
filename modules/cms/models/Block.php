<?php
	class Block extends Model{
		protected function init(){
			$this->tabla = "cms_block";
			$this->module = "cms";
		}
	}