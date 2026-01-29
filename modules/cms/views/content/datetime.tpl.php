<?php 
	$conf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
	$values = array();
	if($conf->num_values == 1){
		if(empty($content)){
			($value = new stdClass());
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

	$min = "";
	if(!empty($conf->min_value)){
		$partes = explode(" ", $conf->min_value);
		$min = explode("-", $partes[0]);
		$min2= explode(":", $partes[1]);
	}
	$max = "";
	if(!empty($conf->max_value)){
		$partes = explode(" ", $conf->max_value);
		$max = explode("-", $partes[0]);
		$max2= explode(":", $partes[1]);
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
			<input type="input" id="dateTime<?=$field->id?>" />
			<script>
				$("#dateTime<?=$field->id?>").kendoDateTimePicker({
					format: "yyyy-MM-dd HH:mm:ss"
					<?=(!empty($conf->min_value) ? ",min: new Date(".$min[0].", ".($min[1]-1).", ".$min[2]." , ".$min2[0].", ".$min2[1].")": "")?>
					<?=(!empty($conf->max_value) ? ",max: new Date(".$max[0].", ".($max[1]-1).", ".$max[2]." , ".$max2[0].", ".$max2[1].")": "")?>
				});
			</script>
			<a id="btnAgregarFechaHora<?=$field->id?>">Agregar <?=$conf->label?></a>
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
				$("#btnAgregarFechaHora<?=$field->id?>").kendoButton({
					click: function (e){
						if($("#dateTime<?=$field->id?>").val().trim() == "" ){
							alert("Debes introducir un valor a agregar");
						}
						else{
							$.getJSON(
								"<?=$url("cms/content/addValueField")?>", 
								{
									value: $("#dateTime<?=$field->id?>").val(),
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
										$("#dateTime<?=$field->id?>").val("");
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
				type="input" 
				id="field_<?=$field->id?>" 
				name="field_<?=$field->id?>" 
				value="<?=$value->value_field?>"
				<?=($conf->required == 1 ? "required" : "")?>
				data-required-msg="<?=$field->name." es requerido"?>"
				/>
			<script>
				$("#field_<?=$field->id?>").kendoDateTimePicker({
					format: "yyyy-MM-dd HH:mm:ss"
					<?=(!empty($conf->min_value) ? ",min: new Date(".$min[0].", ".($min[1]-1).", ".$min[2]." , ".$min2[0].", ".$min2[1].")": "")?>
					<?=(!empty($conf->max_value) ? ",max: new Date(".$max[0].", ".($max[1]-1).", ".$max[2]." , ".$max2[0].", ".$max2[1].")": "")?>
				});
			</script>
		<?php endif; ?>
		<?php if($conf->num_values != 1): ?>
		</fieldset>
		<?php endif; ?>

	</div>
</div>