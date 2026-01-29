<?php

  $url = function ($url = null){
    
    if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
      return "https://".$this->burl.$url;
    }
    else{
      return "http://".$this->burl.$url;
    }
  };
  
  $urlm = function ($url = null){
    if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
      return "https://".$this->burl.$this->murl."/".$url;
    }
    else{
      return "http://".$this->burl.$this->murl."/".$url;
    }
  };
  
  $path = function ($path = null) {
    return Motor::app()->absolute_url.$path;
  };
  
  $pathm = function ($path = null) {
    return Motor::app()->absolute_url.$this->murl."/".$path;
  };

  $region = function ($regionName){
    include(Motor::app()->absolute_url."base/Functions.php");
    
    $bandera = false;

    $burl = $_SERVER["HTTP_HOST"].str_replace("index.php", "", $_SERVER["SCRIPT_NAME"]); 

    $region = new Region("WHERE name = '".$regionName."'");
    
    if(empty($region->id)){
      echo "La region ".$regionName." no se encuentra registrada";
    }
    else{
      $bloques = Block::model()->findAll("WHERE region = ".$region->id." ORDER BY order_block");
      foreach($bloques as $bloque){
        $bandera = false;

        $structure = explode("/",  $_SERVER["SCRIPT_FILENAME"]);
        $front_page = $structure[count($structure)-2];
        $page =  $_SERVER["REQUEST_URI"] == "/".$front_page."/" ? "front" : str_replace("/".$front_page."/", "", $_SERVER["REQUEST_URI"]);
        $page = explode("?", $page)[0];
        if($page=="/") $page= "front";
        //echo $page;

        if($bloque->iquals_show_in == 1 && $bloque->show_in != ""){
          $paginas = explode("\n", $bloque->show_in);
          for($i = 0 ; count($paginas) >1 && $i < count($paginas)-1 ; $i++){
            $paginas[$i] = substr($paginas[$i], 0, strlen($paginas)-1);
          }
          if(in_array($page, $paginas)){
            $bandera = true;
          }
        }
        else if($bloque->iquals_show_in == 0 && $bloque->show_in != ""){
          $paginas = explode("\n", str_replace(" ","",$bloque->show_in));
          for($i = 0 ; count($paginas) >1 && $i < count($paginas)-1; $i++){
            $paginas[$i] = substr($paginas[$i], 0, strlen($paginas)-1);
          }
          if(!in_array($page, array_values($paginas))){
            $bandera = true;
          }
        }
        else if($bloque->iquals_show_in == 1 && $bloque->show_in == ""){
          $bandera = false;
        }
        else{
          $bandera = true;
        }
        
        if($bandera){
          
          if(!empty($bloque->list_data)){

            $listDataModel = new ListData($bloque->list_data);

            $data = array();
            if(!empty($listDataModel->query)){
              foreach($listDataModel->executeQuery($listDataModel->query) as $row){
                $data[$row->id]["titulo"] = $row->titulo;
                $data[$row->id]["usuario"] = $row->usuario;
                $data[$row->id]["liga"] = $row->liga;
                $data[$row->id]["creado"] = $row->creado;
                $data[$row->id]["modificado"] = $row->modificado;
                $data[$row->id]["active"] = $row->active;
                $data[$row->id][$row->campo] = $row->campo;
                $data[$row->id][$row->campo] = html_entity_decode($row->valor);
                $data[$row->id]["data"]["titulo"] = $row->titulo;
                $data[$row->id]["data"]["usuario"] = $row->usuario;
                $data[$row->id]["data"]["liga"] = $row->liga;
                $data[$row->id]["data"]["creado"] = $row->creado;
                $data[$row->id]["data"]["modificado"] = $row->modificado;
                $data[$row->id]["data"]["active"] = $row->active;
                $data[$row->id]["data"][$row->campo] = $row->campo;
                $data[$row->id]["data"][$row->campo] = html_entity_decode($row->valor);
              }
            }
            if($listDataModel->type == 0){
              include_once(Motor::app()->absolute_url.'base/xtemplate.class.php');
              
              

              $content  = "<!-- BEGIN: main -->\n";
              $content .= $listDataModel->template;
              $content .= "<!-- END: main -->";
              $xtpl = new XTemplate($content);

              foreach($data as $key => $value){
                // assign array data
                
                $xtpl->insert_loop('main.rows', array('data'=>$value["data"]));
                $xtpl->insert_loop('main.rowsdata', array('data'=>$value));

                // parse a row
                
              }
              if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
                $xtpl->assign('burl', "https://".$this->burl);
              }
              else{
                $xtpl->assign('burl', "http://".$this->burl);
              }
              if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
                $xtpl->assign('murl', "https://".$this->burl.$this->murl."/");
              }
              else{
                $xtpl->assign('murl', "https://".$this->burl.$this->murl."/");
              }
              $xtpl->parse('main');
              $xtpl->out('main');
            }
            else{



              $archivo = str_replace(".tpl.php","",Motor::app()->absolute_url."templates/list_data/".$listDataModel->file).".tpl.php";

              if(!file_exists($archivo))
              {
                echo "el archivo de vista ".$listDataModel->file." no existe";
              }  
              else{
                
                if(!empty($listDataModel->content_type)){
                  $sql = "
                    SELECT 
                      contenido.id AS id,
                      contenido.name AS titulo,
                      contenido.url AS liga,
                      usuario.username AS usuario,
                      contenido.created AS creado,
                      contenido.modified AS modificado,
                      tcampo.name AS campo,
                      tvalor.value_field AS valor
                    FROM
                      cms_content AS contenido
                    INNER JOIN 
                      cms_content_type AS tipo_contenido ON contenido.content_type = tipo_contenido.id
                    INNER JOIN 
                      user AS usuario ON contenido.user = usuario.id
                    LEFT JOIN 
                      cms_content_type_field_type as tcampo ON tipo_contenido.id = tcampo.content_type
                    LEFT JOIN 
                      cms_content_value AS tvalor ON contenido.id = tvalor.content AND tvalor.content_type_field_type = tcampo.id
                    WHERE
                      tipo_contenido.id = ".$listDataModel->content_type." AND
                      contenido.published = 1
                    ORDER BY contenido.id DESC, tcampo.id
                  ";

                  $data = Content::model()->executeQuery($sql);
                  $listData = array();
                  foreach($data as $row){
                    $listData[$row->id]["id"] = $row->id;
                    $listData[$row->id]["titulo"] = $row->titulo;
                    $listData[$row->id]["liga"] = $row->liga;
                    $listData[$row->id]["usuario"] = $row->usuario;
                    $listData[$row->id]["creado"] = $row->creado;
                    $listData[$row->id]["modificado"] = $row->modificado;
                    $listData[$row->id]["campos"][$row->campo][] = $row->valor;
                  }
                  $count = 0;
                  foreach($listData as $row){
                    $listView[$count] = $row;
                    $count++;
                  }
                }
                
                include($archivo);
              }
            }
          }
          else if($bloque->content_type == 0){
            
            //echo strpos($bloque->content, "{region:");exit();
          
            include_once(Motor::app()->absolute_url.'base/xtemplate.class.php');
            $content  = "<!-- BEGIN: main -->\n";
            $content .= $bloque->content;
            $content .= "<!-- END: main -->";
            $xtpl = new XTemplate($content);
            if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
              $xtpl->assign('burl', "https://".$this->burl);
            }
            else{
              $xtpl->assign('burl', "http://".$this->burl);
            }
            if(isset(Motor::app()->https[$this->module]) && Motor::app()->https[$this->module]){
              $xtpl->assign('murl', "https://".$this->burl.$this->murl."/");
            }
            else{
              $xtpl->assign('murl', "http://".$this->burl.$this->murl."/");
            }
            $xtpl->parse('main');
            $xtpl->out('main');
          }
          else if($bloque->content_type == 1){
            echo html_entity_decode($bloque->content);
          }
          else{
            $archivo = str_replace(".tpl.php","",Motor::app()->absolute_url."templates/block/".$bloque->content).".tpl.php";
            if(!file_exists($archivo))
            {
              echo "el archivo de vista ".$archivo." no existe";
            }
            else{
              include($archivo);
            }
          }
        }
      }
    }
  };
  
  $getArticle = function ($id = null){
    $article = array();
    if(!empty($id)){
      $sql = "
        SELECT 
          0 AS id,
          contenido.name AS titulo,
          contenido.url AS liga,
          usuario.username AS usuario,
          contenido.created AS creado,
          contenido.modified AS modificado,
          tcampo.name AS campo,
          
          tvalor.value_field AS valor
        FROM
          cms_content AS contenido
        INNER JOIN 
          cms_content_type AS tipo_contenido ON contenido.content_type = tipo_contenido.id
        INNER JOIN 
          user AS usuario ON contenido.user = usuario.id
        LEFT JOIN 
          cms_content_type_field_type as tcampo ON tipo_contenido.id = tcampo.content_type
        LEFT JOIN 
          cms_content_value AS tvalor ON contenido.id = tvalor.content AND tvalor.content_type_field_type = tcampo.id
        WHERE
          contenido.id = ".$id." AND
          contenido.published = 1
        ORDER BY tcampo.id
      ";
      $data = Content::model()->executeQuery($sql);
      foreach($data as $row){
        $article[$row->id]["titulo"] = $row->titulo;
        $article[$row->id]["liga"] = $row->liga;
        $article[$row->id]["usuario"] = $row->usuario;
        $article[$row->id]["creado"] = $row->creado;
        $article[$row->id]["modificado"] = $row->modificado;
        $article[$row->id]["campos"][$row->campo][] = $row->valor;
      }
    }
    return $article[0];
  };

  $getListData = function ($id){
    $sql = "
      SELECT 
        contenido.id AS id,
        contenido.name AS titulo,
        contenido.url AS liga,
        usuario.username AS usuario,
        contenido.created AS creado,
        contenido.modified AS modificado,
        tcampo.name AS campo,
        tvalor.value_field AS valor
      FROM
        cms_content AS contenido
      INNER JOIN 
        cms_content_type AS tipo_contenido ON contenido.content_type = tipo_contenido.id
      INNER JOIN 
        user AS usuario ON contenido.user = usuario.id
      LEFT JOIN 
        cms_content_type_field_type as tcampo ON tipo_contenido.id = tcampo.content_type
      LEFT JOIN 
        cms_content_value AS tvalor ON contenido.id = tvalor.content AND tvalor.content_type_field_type = tcampo.id
      WHERE
        tipo_contenido.id = ".$id." AND
        contenido.published = 1
      ORDER BY contenido.id ASC, tcampo.id
    ";



    $data = Content::model()->executeQuery($sql);
    $listData = array();
    $listView = array();
    foreach($data as $row){
      $listData[$row->id]["id"] = $row->id;
      $listData[$row->id]["titulo"] = $row->titulo;
      $listData[$row->id]["liga"] = $row->liga;
      $listData[$row->id]["usuario"] = $row->usuario;
      $listData[$row->id]["creado"] = $row->creado;
      $listData[$row->id]["modificado"] = $row->modificado;
      $listData[$row->id]["campos"][$row->campo][] = $row->valor;
    }
    $count = 0;
    foreach($listData as $row){
      $listView[$count] = $row;
      $count++;
    }
    return $listView;
  };

  $obtenerCadena = function($contenido,$inicio,$fin){
      $r = explode($inicio, $contenido);
      if (isset($r[1])){
          $r = explode($fin, $r[1]);
          return $r[0];
      }
      return '';
  };

  $imprimirArray = function($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
  };

  
?>