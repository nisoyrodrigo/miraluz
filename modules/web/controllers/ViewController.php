<?php
  class ViewController extends Controller{
    public function actionInit(){
      
      $model = new Content($this->params["id_content_cms"]);
      $contentType = new ContentType($model->content_type);
      
      $content = array();
      foreach($model->campos as $campo){
        $content[$campo["nombre"]] = $campo["valor"];
      }

      $fields = array();
      foreach(ContentTypeFieldType::model()->findAll("WHERE content_type = ".$model->content_type) as $field){
        $valor = new ContentValue("WHERE content = ".$model->id." AND content_type_field_type = ".$field->id);
        $fields[$field->name] = html_entity_decode($valor->value_field);
      }
      
      if(empty($model->template)){
        
        $content["fields"] = $fields;
        $this->render("index", array("content" => $content));
      }
      else{
        
        $template = new Template($model->template);
        
        if($template->template_type == 0){

          include_once(Motor::app()->absolute_url.'base/xtemplate.class.php');
          include_once(Motor::app()->absolute_url.'base/Functions.php');

          $regiones = Region::model()->findAll();

          foreach($regiones as $row){
            if(strpos($template->content, "[region.".$row->name."]") > 0){
              ob_start();
              $region($row->name);
              $string = ob_get_contents();
              ob_end_clean();



              $template->content = str_replace("[region.".$row->name."]", $string, $template->content);
            }
          }



          $contenido  = "<!-- BEGIN: main -->\n";
          $contenido .= $template->content;
          $contenido .= "<!-- END: main -->";
          $xtpl = new XTemplate($contenido);
          foreach($content as $nombre => $valor){
            $xtpl->assign($nombre, $valor);
          }
          foreach($fields as $nombre => $valor){
            $xtpl->assign($nombre, $valor);
          }


          $xtpl->assign('data', $content);
          $xtpl->assign('burl', $this->burl);
          $xtpl->assign('murl', $this->murl);
          $xtpl->parse('main');
          ob_start();
          $xtpl->out('main');
          $content = ob_get_contents();
          ob_end_clean();
          $this->renderByString($content);
        }
        else{
          $content["fields"] = $fields;
          $this->render("templates/".str_replace(".tpl.php", "", $template->content), array("content" => $content), true);
        }
      }     
      
    }
  }