<?php
  class DefaultController extends Controller{
    
    public function actionInit(){
      $this->render("index");
    }

    public function actionCheckPedido(){
      $this->template = null;
      $aSalida = array();
      $model = new Venta("WHERE folio = '".$this->params["venta"]."' AND clave = '".$this->params["clave"]."'");
      if($model->id != ""){
        $estatus = new VentaEstatus($model->estatus);
        $cliente = new Cliente($model->cliente);
        $model->cliente_obj = $cliente;
        $model->estatus_obj = $estatus;
      }
      $aSalida["data"] = $model;
      $this->renderJSON($aSalida);
    }

    public function actionSolicitarFactura(){
      $this->template = null;
      $aSalida = array();
      $model = new Venta("WHERE folio = '".$this->params["venta"]."' AND clave = '".$this->params["clave"]."'");

      if($model->id == ""){
        $this->error = "No existe un pedido con ese número de nota y clave";
      }

      if($this->error == ""){
        $abonos = VentaMovimiento::model()->executeQuery("SELECT SUM(monto) AS total FROM ec_venta_movimiento WHERE tipo = 'ingreso' AND numero != 1 AND venta = ".$model->id)[0]->total;
        // $rowsquery[$key]->abonos = $abonos ?? 0;
        $saldo = $model->saldo - $abonos;

        if($saldo>0){
          $this->error .= "No puedes facturar un pedido que aún no liquidas.";
        }

      }
  
      $solicitud = new VentaFacturaSolicitud("WHERE venta = ".$model->id);
      if($solicitud->id != ""){
        $this->error .= "Ya solicitaste la factura para este pedido.";
      }

      if($this->error == ""){
        $solicitud->venta = $model->id;
        $solicitud->correo = $this->params["correo"];
        $solicitud->uso_cfdi = $this->params["cfdi"];
        $solicitud->razon = $this->params["razon"];
        $solicitud->rfc = $this->params["rfc"];
        $solicitud->regimen_fiscal   = $this->params["regimen_fiscal"];
        $solicitud->direccion_fiscal = $this->params["direccion_fiscal"];
        $solicitud->codigo_postal    = $this->params["codigo_postal"];
        $solicitud->observaciones    = $this->params["observaciones"] ?? "";
        if(!$solicitud->save()){
          $this->error = "No se pudo registrar tu solicitud.";
        } else {
          $solicitud->getAttributes();
        }
      }

      // === SUBIR CONSTANCIA FISCAL (PDF) ===
      if(isset($_FILES["constancia_fiscal"]) && $this->error == ""){

        $dir_subida = Motor::app()->absolute_url.$this->murl."/archivos/facturas/";

        if(!file_exists($dir_subida)){
          mkdir($dir_subida, 0775, true);
        }

        $path = $_FILES['constancia_fiscal']['name'];
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        // Seguridad básica
        if($ext != "pdf"){
          $this->error = "La constancia fiscal debe ser un archivo PDF.";
        } else {
          $solicitud = new VentaFacturaSolicitud($solicitud->id);
          $nombre_archivo = $solicitud->id."_constancia_fiscal.pdf";
          $fichero_guardado = "archivos/facturas/".$nombre_archivo;
          $fichero_subido   = $dir_subida."/".$nombre_archivo;

          if(!move_uploaded_file($_FILES['constancia_fiscal']['tmp_name'], $fichero_subido)){
            $this->error = "Error al guardar la constancia fiscal.";
          } else {
            $solicitud->constancia_fiscal = $this->murl."/".$fichero_guardado;
            $solicitud->save(); // actualizar ruta
          }

        }
      }


      $aSalida["error"] = $this->error;
      $aSalida["exito"] = ($this->error == "") ? true:false;

      $this->renderJSON($aSalida);
    }

  }
?>