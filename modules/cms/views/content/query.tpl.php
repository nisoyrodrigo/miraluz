<?php 
	$conf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
	$values = array();
	if($conf->num_values == 1){
		if(empty($content)){
			$value = $conf->default_value;
		}else{
			$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
		}
		if(empty($value->value_field)){
			$value->value_field = $conf->default_value;
		}
	}
	else{
		$values = ContentValue::model()->findAll("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
	}
?>
<div class="row">
	<div class="col-lg-12">
		<?php if($conf->num_values != 1): ?>
		<fieldset>
			<legend><?=$conf->label?></legend>
		<?php else: ?>
			<label><?=$conf->label?></label><br/>
		<?php endif; ?>

		<?php if($conf->num_values != 1): ?>
			<input 
				id="autocompletar_<?=$field->id?>"  
				style="width:100%" 
				value=""
				/>
			<script>
				$("#autocompletar_" + <?=$field->id?>).kendoMultiSelect({
		            placeholder: "Selecciona...",
		            dataTextField: "text",
		            dataValueField: "value",
		            multiple: true,
		            separator: ";",
		            filter: "contains",
		            autoBind: true,
		            minLength: 3,
		            dataSource: {
		                type: "jsonp",
		                serverFiltering: true,
		                transport: {
		                    read: {
		                        url: "<?=$url("cms/content/getFieldQuery")?>?valor=&conf_id=" + <?=$conf->id?>,
		                    }
		                }
		            }
		        });
			</script>
			<a id="btnAgregarQuery<?=$field->id?>">Agregar <?=$conf->label?></a>
			<div id="grid<?=$field->id?>" style="margin-top:10px"></div>
			<script>
				$("#grid<?=$field->id?>").kendoGrid({
					dataSource: {
						transport: {
							read: {
								url: "<?=$url("cms/content/getValueForField?field=".$field->id."&content=".$content->id)?>",
								type: "POST",
								dataType: "jsonp",
								contentType: "application/json; charset=utf-8"
							},
							destroy: {
								url: "<?=$url("cms/content/deleteValueField")?>",
								dataType: "jsonp",
								type: "POST"
							}
						},
						error: function (e) {
							notification.show({
								title: "!Aviso",
								message: e.xhr.responseText
							}, "error");
							if(accion == "destroy"){
								$("#grid").data("kendoGrid").dataSource.read();
							}
						},
						schema: {
							model: {
								id : "id",
								fields: {   
									id: { editable: false },
									value_field: { type: "string", validation: { required: true} }
								}
							}
						}
					},
					height: 350,
					sortable: false,
					reorderable: false,
					resizable: false,
					pageable: false,
					columns: [
						{
							field: "id",
							hidden: true
						},
						{
							field: "value_field",
							title: "&nbsp;",
							width: 250
						},
						{ command: [{name: "destroy", text: "Eliminar"}], title: "&nbsp;", width: 200 }
					],
					editable: "inline",
				});
				$("#btnAgregarQuery<?=$field->id?>").kendoButton({
					click: function (e){
						if($("#autocompletar_<?=$field->id?>").data("kendoMultiSelect").value() == "" ){
							alert("Debes introducir un valor a agregar");
						}
						else{
							$.getJSON(
								"<?=$url("cms/content/addValueField")?>", 
								{
									value: $("#autocompletar_<?=$field->id?>").data("kendoMultiSelect").value(),
									content: $("#id").val(),
									id_field: <?=$field->id?>
								},
								function(response){
									if(response.id == undefined){
										notification.show({
											title: "!Aviso",
											message: response
										}, "error");
									}
									else if(response.error == undefined){
										notification.show({
											title: "!Aviso",
											message: response.error
										}, "error");
									}
									else{
										$("#autocompletar_<?=$field->id?>").data("kendoMultiSelect").value();
										$("#id").val(response.content);
										$("#grid<?=$field->id?>").data("kendoGrid").dataSource.transport.options.read.url = "<?=$url("cms/content/getValueForField?field=".$field->id."&content=")?>" + response.content;
										$("#grid<?=$field->id?>").data("kendoGrid").dataSource.read();
									}
								}
							);
						}
					}
				});
			</script>
		<?php else: ?>
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
				value=<?=str_replace("]", "", str_replace("[", "", json_encode(explode(",",$value->value_field))))?>
				/>
			<script>
				$("#autocompletar_<?=$field->id?>").kendoMultiSelect({
		            placeholder: "Selecciona...",
		            dataTextField: "text",
		            dataValueField: "value",
		            multiple: true,
		            separator: ";",
		            filter: "contains",
		            autoBind: true,
		            minLength: 3,
		            value: <?=json_encode(explode(",",$value->value_field))?>,
		            dataSource: {
		                type: "jsonp",
		                serverFiltering: true,
		                transport: {
		                    read: {
		                        url: "<?=$url("cms/content/getFieldQuery")?>?valor=" + "<?=$value->value_field?>" + "&conf_id=" + <?=$conf->id?>,
		                    }
		                }
		            },
		            change:function (e){
						$("#field_" + <?=$field->id?>).val(e.sender.value()); 
		            }
		        });
			</script>
		<?php endif; ?>
		<?php if($conf->num_values != 1): ?>
		</fieldset>
		<?php endif; ?>

	</div>
</div>