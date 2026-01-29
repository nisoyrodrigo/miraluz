<?php 
	$conf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
	$values = array();
	if($conf->num_values == 1){
		if(empty($content)){
			$valuec = new stdClass();
		}else{
			$valuec = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
		}
		if(empty($valuec->value_field)){
			$valuec->value_field = $conf->default_value;
		}
	}
	else{
		$values = ContentValue::model()->findAll("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
	}
	
	$values_parse = explode("\n", $conf->allowed_values);
	
	
	$values_combo = array();
	foreach($values_parse as $value){
		$value_parse = explode("|", $value);
		
		if(count($value_parse == 2)){
			if($value_parse[0] != "")
				$values_combo[] = array("value" => $value_parse[0], "text" => $value_parse[1]); 
		}
		else{
			if(!empty($value_parse))
				$values_combo[] = array("value" => $value_parse, "text" => $value_parse); 
		}
		
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
			<select
				name="select<?=$field->id?>" 
				id="select<?=$field->id?>" 
			>
			</select><a id="btnAgregarSelect<?=$field->id?>">Agregar <?=$conf->label?></a>
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
				$("#btnAgregarSelect<?=$field->id?>").kendoButton({
					click: function (e){
						if($("#select<?=$field->id?>").data("kendoComboBox").value().trim() == "" ){
							alert("Debes introducir un valor a agregar");
						}
						else{
							$.getJSON(
								"<?=$url("cms/content/addValueField")?>", 
								{
									value: $("#select<?=$field->id?>").data("kendoComboBox").value(),
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
										$("#select<?=$field->id?>").data("kendoComboBox").value("");
										$("#id").val(response.content);
										$("#grid<?=$field->id?>").data("kendoGrid").dataSource.transport.options.read.url = "<?=$url("cms/content/getValueForField?field=".$field->id."&content=")?>" + response.content;
										$("#grid<?=$field->id?>").data("kendoGrid").dataSource.read();
									}
								}
							);
						}
					}
				});
				
				$("#select<?=$field->id?>").kendoComboBox({
					dataTextField: "text",
					dataValueField: "value",
					dataSource: <?=json_encode($values_combo)?>,
					filter: "contains",
					suggest: true
				}).data("kendoComboBox").value();
			</script>
		<?php else: ?>
			<select
				type="text" 
				name="field_<?=$field->id?>" 
				id="field_<?=$field->id?>" 
				data-required-msg="<?=$field->name." es requerido"?>"
				<?=($conf->required == 1 ? "required" : "")?>
			>
			</select>
			<script>
				$("#field_<?=$field->id?>").kendoComboBox({
					dataTextField: "text",
					dataValueField: "value",
					dataSource: <?=json_encode($values_combo)?>,
					filter: "contains",
					suggest: true
				}).data("kendoComboBox").value('<?=$valuec->value_field?>');
			</script>
		<?php endif; ?>
		<?php if($conf->num_values != 1): ?>
		</fieldset>
		<?php endif; ?>

	</div>
</div>