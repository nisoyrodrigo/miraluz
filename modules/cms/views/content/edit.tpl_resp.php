<script>
	function eliminarImagen(id){
		if(confirm("¿Deseas eliminar el archivo?")){
			var content_id = $("#id").val();
			$.get("<?=$url("cms/content/deletefile?content=")?>" + content_id + "&id_field=" + id, function(response){
				if(response == ""){
					$("#divFile" + id).html('<input type="file" id="file_' + id + '" name="file_' + id + '">');
					$("#imagenVisible" + id).css("display", "none");
					$("#imagen-por-defecto" + id).attr("src", "");
					cargaControlArchivo(id);
				}
				else if(response.error != undefined){
					if(response.error == ""){
						$("#divFile" + id).html('<input type="file" id="file_' + id + '" name="file_' + id + '">');
						$("#imagenVisible" + id).css("display", "none");
						$("#field_" + id).attr("value", "");
						$("#imagen-por-defecto" + id).attr("src", "");
						cargaControlArchivo(id);
					}
					else{
						notification.show({
							title: "!Aviso",
							message: response
						}, "error");
					}
				}
				else{
					notification.show({
						title: "!Aviso",
						message: response
					}, "error");
				}
				
			});
		}
	}

	function eliminarVideo(id){
        if(confirm("¿Deseas eliminar el archivo?")){
            $.get("<?=$url("cms/content/deletefile?content=".$content->id."&id_field=")?>" + id, function(response){
                if(response == ""){
                    $("#divFile").html('<input type="file" id="file_' + id + '" name="file_' + id + '">');
                    $("#videoVisible").css("display", "none");
                    $("#video-por-defecto").attr("src", "");
                    cargaControlArchivo(id);
                }
                else if(response.error != undefined){
                    if(response.error == ""){
                        $("#divVideo").html('<input type="file" id="file_' + id + '" name="file_' + id + '">');
                        $("#imagenVisible").css("display", "none");
                        $("#field_" + id).attr("value", "");
                        $("#video-por-defecto").find("source").attr("src", "");
                        cargaControlArchivo(id);
                    }
                    else{
                        notification.show({
                            title: "!Aviso",
                            message: response
                        }, "error");
                    }
                }
                else{
                    notification.show({
                        title: "!Aviso",
                        message: response
                    }, "error");
                }
                
            });
        }
    }

    function cargaControlVideo(id){
        $("#file_" + id).kendoUpload({
            multiple: false,
            async: {
                saveUrl: "<?=$url("cms/content/savefile?content=".$content->id."&id_field=")?>" + id + "&content_type=<?=$contentType->id?>",
                autoUpload: true
            },
            success: function (e) {
                $("#id").val(e.response.content);
                $("#videoVisible").css("display", "block");
                $("#field_" + id).attr("value", "<?=$url()?>" + e.response.value_field);
                $("#video-por-defecto").find("source").attr("src", e.response.value_field);
            }
        });
    }
	
	function cargaControlArchivo(id){
		$("#file_" + id).kendoUpload({
			multiple: false,
			async: {
				saveUrl: "<?=$url("cms/content/savefile?content=".$content->id."&id_field=")?>" + id + "&content_type=<?=$contentType->id?>",
				autoUpload: true
			},
			success: function (e) {
				$("#id").val(e.response.content);
				$("#imagenVisible" + id).css("display", "block");
				$("#field_" + id).attr("value", e.response.value_field);
				$("#imagen-por-defecto" + id).attr("src", "<?=$url()?>" + e.response.value_field);
			}
		});
	}

	function cargaControlQuery(id, valor, conf_id){
		$("#autocompletar_" + id).kendoMultiSelect({
            placeholder: "Selecciona...",
            dataTextField: "text",
            dataValueField: "value",
            multiple: true,
            separator: ";",
            filter: "contains",
            autoBind: true,
            minLength: 3,
            value: valor,
            dataSource: {
                type: "jsonp",
                serverFiltering: true,
                transport: {
                    read: {
                        url: "<?=$url("cms/content/getFieldQuery")?>?valor=" + valor + "&conf_id=" + conf_id,
                    }
                }
            },
            change:function (e){
            	var valorActual = e.sender.value();
            	$("#field_" + id).val(valorActual); 
            }
        });
	}
	
	function agregarElementoImagen(fieldset, id){
		$("#fieldSet" + fieldset).append('<div class="col-lg-8" id="divFile' + id + '">'+
								'<input type="file" id="file_' + id + '" name="file_' + id + '">'+
							'</div>' +
							'<div id="imagenVisible' + id + '" style="">' +
								'<input type="hidden" id="field_' + id + '" name="field_8[1]" value=""/>' +
								'<center>' +
									'<img src="" id="imagen-por-defecto' + id + '" height="100px" >' +
									'<a href="javascript:eliminarImagen(\'' + id + '\')" class="k-button">Eliminar</a>' +
								'</center>' +
							'</div>');
							cargaControlArchivo(id);
							
	}

</script>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/content")?>">Contenido</a> : Editar
		</div>
	</div>
	<form id="form-content">
		<input type="hidden" name="id" id="id" value="<?=$content->id?>"/>
		<input type="hidden" name="content_type" value="<?=$content->content_type?>"/> 
		<div class="row">
			<div class="col-lg-12">
				<label><h3>Titulo</h3></label>
			</div>
			<div class="col-lg-12">
				<input 
					size="50"
					style="width:100%"
					type="text" 
					name="name" 
					id="name" 
					class="k-input k-textbox"
					value="<?=($content->name=="temp"?"":$content->name)?>"
					required
					/>
			</div>
		</div>
	<?php 
		$content_type_field_type = "";
		$cont = 1;
		
		foreach($fields as $field): 
			$banderaCerrar = false;
			$valor = empty($content->id) ? $fields_conf[$field->id]->default_value : $values[$field->value_id]->value_field;
			if($fields_conf[$field->id]->content_type_field_type != $content_type_field_type){
				echo '<div class="row"><div class="col-lg-12">';
				$content_type_field_type = $fields_conf[$field->id]->content_type_field_type; 
				$cont = 1;
				$banderaCerrar = true;
				if($fields_conf[$field->id]->num_values == 1){
					echo '<label for="'.$field->name.'">'.$field->name.'</label><br/>';
				}
				elseif($fields_conf[$field->id]->num_values == 0){
					echo '<fieldset id="fieldSet'.$field->name.'"><legend >'.$field->name.' (Total valores : Multiple)</legend>';
				}
				else{
					echo '<fieldset id="fieldSet'.$field->name.'"><legend >'.$field->name.' (Total valores : '.$fields_conf[$field->id]->num_values.')</legend>';
				}
			}
			$field_array = "";
			if($fields_conf[$field->id]->num_values <> 1 ){
				$field_array = "[$cont]";
			}
	?>
		
				
					
				<?php 
					switch($field->field_type):
						case '1'://Text ?>
		

							<input 
								size="<?=$fields_conf[$field->id]->size_field?>"
								style="width:100%"
								type="text" 
								name="field_<?=$field->id?>" 
								id="field_<?=$field->value_id?>" 
								class="k-input k-textbox"
								data-required-msg="<?=$field->name." es requerido"?>"
								value="<?=(isset($values[$field->value_id]) ? $values[$field->value_id]->value_field : "")?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								/>
							
				<?php 
							break;
						case '2'://LongText ?>
							<textarea 
								style="width:100%;height:500px"
								rows="20"
								id="field_<?=$field->id?>"
								name="field_<?=$field->id?>"
								data-required-msg="<?=$field->name." es requerido"?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>><?=$valor?></textarea>
								<script>
									$("#field_<?=$field->id?>").kendoEditor({tools: [
						                "bold",
						                "italic",
						                "underline",
						                "strikethrough",
						                "justifyLeft",
						                "justifyCenter",
						                "justifyRight",
						                "justifyFull",
						                "insertUnorderedList",
						                "insertOrderedList",
						                "indent",
						                "outdent",
						                "createLink",
						                "unlink",
						                "insertImage",
						                "insertFile",
						                "subscript",
						                "superscript",
						                "tableWizard",
						                "createTable",
						                "addRowAbove",
						                "addRowBelow",
						                "addColumnLeft",
						                "addColumnRight",
						                "deleteRow",
						                "deleteColumn",
						                "viewHtml",
						                "formatting",
						                "cleanFormatting",
						                "fontName",
						                "fontSize",
						                "foreColor",
						                "backColor",
						                "print"
			            			]});
								</script>
				<?php	
							break;
						case '3'://Number 
							$min = "";
							if(!empty($fields_conf[$field->id]->min_value)){
								$min = explode("-", $fields_conf[$field->id]->min_value);
							}
							$max = "";
							if(!empty($fields_conf[$field->id]->max_value)){
								$max = explode("-", $fields_conf[$field->id]->max_value);
							}
				?>
							<input 
								type="text" 
								name="field_<?=$field->id?>" 
								id="field_<?=$field->id?>" 
								data-required-msg="<?=$field->name." es requerido"?>"
								value="<?=(isset($values[$field->value_id]) ? $values[$field->value_id]->value_field : "")?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								/>
							<script>
								$("#field_<?=$field->id?>").kendoNumericTextBox({
									format: "0"
									<?=(!empty($fields_conf[$field->id]->min_value) ? ",min: ".$fields_conf[$field->id]->min_value: "")?>
									<?=(!empty($fields_conf[$field->id]->max_value) ? ",max: ".$fields_conf[$field->id]->max_value : "")?>
								});
							</script>
				<?php
							break;
						case 6://ComboBox
							$values_parse = explode("\n", $fields_conf[$field->id]->allowed_values);
							$values = array();
							foreach($values_parse as $value){
								$value_parse = explode("|", $value);
								if(count($value_parse == 2)){
									if(!empty($value_parse[0]))
										$values[] = array("value" => $value_parse[0], "text" => $value_parse[1]); 
								}
								else{
									if(!empty($value_parse))
										$values[] = array("value" => $value_parse, "text" => $value_parse); 
								}
								
							}
				?>
							<select
								type="text" 
								name="field_<?=$field->id?>" 
								id="field_<?=$field->id?>" 
								data-required-msg="<?=$field->name." es requerido"?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
							>
							</select>
							<script>
								$("#field_<?=$field->id?>").kendoComboBox({
									dataTextField: "text",
									dataValueField: "value",
									dataSource: <?=json_encode($values)?>,
									filter: "contains",
									suggest: true
								}).data("kendoComboBox").value(<?=(isset($values[$field->value_id]) ? $values[$field->value_id]->value_field : "")?>);
							</script>
				<?php	
							break;
						case '8': ?>
							<div class="col-lg-8" id="divFile<?=$field->id?>_<?=$cont?>">
								<input type="file" id="file_<?=$field->id?>_<?=$cont?>" name="file_<?=$field->id?>_<?=$cont?>">
							</div>
							<div id="imagenVisible<?=$field->id?>_<?=$cont?>" style="<?=empty($valor) ? ( empty($conf->default_value) ? "display:none" : "") : ""?>">
								<input type="hidden" id="field_<?=$field->id?>_<?=$cont?>" name="field_<?=$field->id?><?=$field_array?>" value="<?=$valor?>"/> 
								<center>
									<img src="<?=$url($valor)?>" id="imagen-por-defecto<?=$field->id?>_<?=$cont?>" height="100px" >
									<a href="javascript:eliminarImagen('<?=$field->id?>_<?=$cont?>')" class="k-button">Eliminar</a>
								</center>
							</div>
							<script>
								cargaControlArchivo("<?=$field->id?>_<?=$cont?>");
							</script>
							<br>
				<?php	
							break;
						case '9': //Date
							$min = "";
							if(!empty($fields_conf[$field->id]->min_value)){
								$min = explode("-", $fields_conf[$field->id]->min_value);
							}
							$max = "";
							if(!empty($fields_conf[$field->id]->max_value)){
								$max = explode("-", $fields_conf[$field->id]->max_value);
							}
				?>
							<input 
								type="input" 
								id="field_<?=$field->id?>" 
								name="field_<?=$field->id?>" 
								value="<?=$valor?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								data-required-msg="<?=$field->name." es requerido"?>"
								/>
							<script>
								$("#field_<?=$field->id?>").kendoDatePicker({
									format: "yyyy-MM-dd"
									<?=(!empty($fields_conf[$field->id]->min_value) ? ",min: new Date(".$min[0].", ".($min[1]-1).", ".$min[2].")": "")?>
									<?=(!empty($fields_conf[$field->id]->max_value) ? ",max: new Date(".$max[0].", ".($max[1]-1).", ".$max[2].")": "")?>
								});
							</script>
				<?php	
							break;
						case '10'://DateTime
							$min = "";
							if(!empty($fields_conf[$field->id]->min_value)){
								$partes = explode(" ", $fields_conf[$field->id]->min_value);
								$min = explode("-", $partes[0]);
								$min2= explode(":", $partes[1]);
							}
							$max = "";
							if(!empty($fields_conf[$field->id]->max_value)){
								$partes = explode(" ", $fields_conf[$field->id]->max_value);
								$max = explode("-", $partes[0]);
								$max2= explode(":", $partes[1]);
							}
				?>
							<input 
								type="input" 
								id="field_<?=$field->id?>" 
								name="field_<?=$field->id?>" 
								value="<?=$valor?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								data-required-msg="<?=$field->name." es requerido"?>"
								/>
							<script>
								$("#field_<?=$field->id?>").kendoDateTimePicker({
									format: "yyyy-MM-dd HH:mm:ss"
									<?=(!empty($fields_conf[$field->id]->min_value) ? ",min: new Date(".$min[0].", ".($min[1]-1).", ".$min[2]." , ".$min2[0].", ".$min2[1].")": "")?>
									<?=(!empty($fields_conf[$field->id]->max_value) ? ",max: new Date(".$max[0].", ".($max[1]-1).", ".$max[2]." , ".$max2[0].", ".$max2[1].")": "")?>
								});
							</script>			
				<?php
							break;
						case '12': ?>
							<input 
								type="email" 
								id="field_<?=$field->id?>" 
								style="width:100%"
								name="field_<?=$field->id?>" 
								class="k-textbox" 
								placeholder="myname@example.net"  
								value="<?=$valor?>"
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								data-email-msg="Formato de email invalido"
								/>
				<?php
            				break;
                        case '13': ?>
                            <div class="col-lg-8" id="divVideo">
                                <input type="file" id="file_<?=$field->id?>" name="file_<?=$field->id?>">

                            </div>
                            <div id="videoVisible" style="<?=empty($valor) ? ( empty($conf->default_value) ? "display:none" : "") : ""?>">
                                <input type="hidden" id="field_<?=$field->id?>" name="field_<?=$field->id?>" value="<?=$valor?>"/> 
                                <center>
                                    <video preload="metadata" id="imagen-por-defecto" height="100px" controls>
                                        <source src="<?=$url($valor)?>">
                                    </video>
                                    <a href="javascript:eliminarVideo(<?=$field->id?>)" class="k-button">Eliminar</a>
                                </center>
                            </div>
                            <script>
                                cargaControlVideo(<?=$field->id?>);
                            </script>
                            <br>
                <?php  
                        break;
						case '14': ?>
							<input 
								id="autocompletar_<?=$field->id?>"  
								style="width:100%" 
								value=""
								/>
							<input 
								type="hidden" 
								id="field_<?=$field->id?>" 
								name="field_<?=$field->id?>" 
								<?=($fields_conf[$field->id]->required == 1 ? "required" : "")?>
								/>
							<script>
								cargaControlQuery(<?=$field->id?>, <?=json_encode(explode(",",$valor))?>, <?=$fields_conf[$field->id]->id?>);
							</script>
								
				<?php
							break;
					endswitch;
					$cont++;
				?>
	<?php
			if($banderaCerrar){
				if($fields_conf[$field->id]->num_values <> 1){
					switch($field->field_type){
						case 8:
							echo '<a href="javascript:agregarElementoImagen(\''.$field->name.'\',\''.$field->id.'_'.($cont + 1).'\')" class="k-button">Agregar '.$field->name.'</a>';
						break;
					}
					echo "</fieldset>";
				}
				
				echo"</div></div>";	
			}
		endforeach; 
	?>
		<div class="row" style="border-top:1px solid #000">
			<div col-lg-12>
				<h1>Configuración</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Publicar</label>
			</div>
			<div class="col-lg-10">
				<select id="published" name="published"></select>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Template</label>
			</div>
			<div class="col-lg-10">
				<select id="template" name="template"></select>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Url</label>
			</div>
			<div class="col-lg-10">
				<input 
						size="200"
						style="width:100%"
						type="text" 
						name="url" 
						id="url" 
						class="k-input k-textbox"
						value="<?=$content->url?>"
						/>
			</div>
		</div>
		<div class="row" style="border-top:1px solid #000;padding-top:15px">
			<div class="col-lg-12"> 
				<a id="btn-guardar" class="k-button">Guardar</a>
				<a id="btn-aplicar" class="k-button">aplicar</a>
				<a id="btn-cancelar" class="k-button">Cancelar</a>
			</div>
		</div>
	</form>
</div>
<script>
	var validator = $("#form-content").kendoValidator().data("kendoValidator");

	$(document).ready(function (){
		$("#published").kendoComboBox({
			dataTextField: "text",
			dataValueField: "value",
			dataSource: [{"text":"Si", "value":"1"},{"text":"No", "value":"0"}],
			filter: "contains",
			suggest: true
		}).data("kendoComboBox").value("<?=$content->published?>");
		
		$("#template").kendoComboBox({
			dataTextField: "text",
			dataValueField: "value",
			dataSource: <?=json_encode($templates)?>,
			filter: "contains",
			suggest: true
		}).data("kendoComboBox").value("<?=(empty($content->id) ? $contentType->template : $content->template)?>");
		
		$("#btn-guardar").click(function (){
			enviar(0);  
		});
		
		$("#btn-aplicar").click(function (){
			enviar(1);  
		});
		
		$("#btn-cancelar").click(function (){
			location.href = '<?=$url("cms/Content")?>';
		});
		
		
	});

	function enviar(tipo){
		if (validator.validate()) {

			$.post('<?=$url("cms/content/save")?>', $("#form-content").serialize(), function(data){
				if(data == ""){
					if(tipo==0){
						location.href = '<?=$url("cms/content")?>';
					}
					else{
						notification.show({
							title: "!Aviso",
							message: "Guardado"
						}, "success");
					}
					$("#id").val(data.id);
				}
				else if(data.error == ""){
					if(tipo==0){
						location.href = '<?=$url("cms/content")?>';
					}
					else{
						notification.show({
							title: "!Aviso",
							message: "Guardado"
						}, "success");
					}
					$("#id").val(data.id);
				}
				else if(data.error == undefined){
					notification.show({
						title: "!Aviso",
						message: data
					}, "error");
				}
				else if(data.error != ""){
					notification.show({
						title: "!Aviso",
						message: data.error
					}, "error");
				}
				
			});
		}
	}
</script>