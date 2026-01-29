<?php
	
	class BlockController extends Controller{
		
		public function actionInit(){
			$regiones = array();
			foreach(Region::model()->findAll() as $region){
				$regiones[] = array("value" => $region->id, "text" => $region->name);
			}
			$this->render("index", array("regiones" => $regiones));
		}
		
		public function actionEdit(){
			$block = new Block($this->params["id"]);
			$regiones = array();
			$regiones[] = array("value" => "", "text" => "Sin region");
			foreach(Region::model()->findAll() as $region){
				$regiones[] = array("value" => $region->id, "text" => $region->name);
			}
			$this->render("edit", array("block" => $block, "regiones" => $regiones));
		}
		
		public function actionGetAll(){
			
			$mm = Block::model()->executeQuery("SELECT MIN(order_block) as minim, MAX(order_block) as maxim FROM cms_block")[0];
			$sql = "
				SELECT 
					id, 
					CONCAT(
						order_block, 
						' ',
						CASE 
							WHEN order_block > ".$mm->minim."
							THEN CONCAT('<a href=\"javascript:ordena(', id, ',\\'up\\')\" class=\"k-icon k-i-arrow-60-up\"></a>')
							ELSE ''
						END ,
						CASE 
							WHEN order_block < ".$mm->maxim."
							THEN CONCAT('<a href=\"javascript:ordena(', id, ',\\'down\\')\" class=\"k-icon k-i-arrow-60-down\"></a>')
							ELSE ''
						END 
					) AS ordenamiento, 
					name, 
					region 
				FROM 
					cms_block
				ORDER BY order_block
			";

			$bloques = Block::model()->executeQuery($sql);
			$this->error = $bloques->error;
			$this->renderJSON($bloques);
		}
		
		public function actionSave(){
			$block = new Block($this->params["id"]);
			
			if($this->params["content_type"] == 2 && strlen($this->params["content"])> 50){
				$this->error = "El nombre del archivo no debe contener mas de 50 caracteres";
			}
			else{
				$block->setAttributes($this->params);
				if(empty($block->id)){
					$resultado = $block->executeQuery("SELECT (max(order_block) + 1) as ordenamiento FROM cms_block");
					$block->order_block = empty($resultado[0]->ordenamiento) ? 1 : $resultado[0]->ordenamiento;
				}
				if(!$block->save()){
					$this->error = $block->error;
				}
			}
			
			$this->renderJSON($block);
			
		}
		
		public function actionDelete(){
			$block = new Block($this->params["id"]);
					
			if(!$block->remove()){
				$this->error = $block->error;
			}
			
			$this->renderJSON();
		}

		public function actionOrder(){
			$block2 = null;
			$block = new Block($this->params["id"]);
			
			if($this->params["direction"] == "up"){
				$block2 = new Block("WHERE (order_block + 1) =  ".$block->order_block." LIMIT 1"); 
				$block->order_block -= 1;
				$block2->order_block += 1;

			}else if($this->params["direction"] == "down"){
				$block2 = new Block("WHERE (order_block - 1) = ".$block->order_block." LIMIT 1");
				$block->order_block += 1;
				$block2->order_block -= 1;
			}

			if(!$block->save()){
				$this->error = "error al reordenar bloque 1";
			}
			else if(!$block2->save()){
				$this->error = "error al reordenar bloque 2";
			}

			$this->renderJSON();
		}
	}