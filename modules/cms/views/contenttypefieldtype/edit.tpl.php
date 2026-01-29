<style>
	.k-button{
		margin-right:15px;
		margin-top:20px
	}
</style>
<form id="form-field">
	<input type="hidden" name="field[id]" value="<?=$field->id?>"/>
	<input type="hidden" name="conf[id]" value="<?=$conf->id?>"/>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/contenttype")?>"/>Tipos de Contenido</a> : <a href="<?=$url("cms/ContentTypeFieldType/init?CotentType=".$contentType->id)?>"><?=$contentType->name?></a> : <?=$field->name?></h1>
			<br/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Nombre del campo *</label> 
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="name" name="field[name]" style="width:100%" value="<?=$field->name?>"/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Tipo de campo *</label>
		</div>
		<div class="col-lg-10">
			<select id="field_type" name="field[field_type]" <?=($field->field_type != null ? "disabled" : "")?>></select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Etiqueta del campo *</label>
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="label" name="conf[label]" value="<?=$conf->label?>"/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Obligatorio </label>
		</div>
		<div class="col-lg-10">
			Si <input type="radio" name="conf[required]" value="1" <?=($conf->required == 1 ? "checked" : "")?>/>&nbsp;&nbsp;
			No <input type="radio" name="conf[required]" value="0" <?=($conf->required == 0 ? "checked" : "")?>/>
		</div>
	</div>
	<?php if(in_array($field->field_type, array(1,2,3))): ?>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Tamaño de dígitos</label>
		</div>
		<div class="col-lg-10">
			<input type="text" name="conf[length_field]" id="length_field" value="<?=$conf->length_field?>">
		</div>
	</div>
	<?php endif; ?>
	<?php if(!in_array($field->field_type, array(5))): ?>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valor por defecto</label>
		</div>
		<div class="col-lg-10">
			<input type="text" name="conf[default_value]" id="default_value" value="<?=$conf->default_value?>">
		</div>
	</div>
	<?php endif; ?>
	<?php if(in_array($field->field_type, array(5))): ?>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Archivo por defecto</label>
		</div>
		<div class="col-lg-10">
			<input type="file" name="default_value_file" id="default_value_file">
		</div>
	</div>
	<?php endif; ?>
	<div class="row">
		<div class="col-lg-2">
			<label>Numero de valores</label>
		</div>
		<div class="col-lg-10">
			<select id="num_values" name="conf[num_values]"></select>
		</div>
	</div>
	<?php if(in_array($field->field_type, array(3, 6, 7))): ?>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valor Minimo</label>
		</div>
		<div class="col-lg-10">
			<input type="text" name="conf[min_value]" id="min_value" value="<?=$conf->min_value?>">
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valor Máximo</label>
		</div>
		<div class="col-lg-10">
			<input type="text" name="conf[max_value]" id="max_value" value="<?=$conf->max_value?>">
		</div>
	</div>
	<?php endif; ?>
	<?php if(in_array($field->field_type, array(4, 5))): ?>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valores permitidos</label>
		</div>
		<div class="col-lg-10">
			<textarea rows="10" cols="20" name="conf[allowed_values]" id="allowed_values"><?=$conf->allowed_values?></textarea>
		</div>
	</div>
	<?php endif; ?>
	<div class="row" style="border-top:1px solid #000">
		<div class="col-lg-12"> 
			<a id="btn-guardar" class="k-button">Guardar</a>
			<a id="btn-aplicar" class="k-button">aplicar</a>
			<a id="btn-cancelar" class="k-button">Cancelar</a>
		</div>
	</div>
</div>
</form>
<script>

	<?php if(in_array($field->field_type, array(1,2,4,5))): ?>

		$("#allowed_values").addClass("k-textBox");
		$("#default_value").addClass("k-textBox");
		$("#length_field").addClass("k-textBox");

	<?php endif; ?>

	<?php if(in_array($field->field_type, array(4,5))): ?>

		$("#default_value_file").kendoUpload();
		$("#allowed_values").addClass("k-textBox");

	<?php endif; ?>

	<?php if($field->field_type == 3): ?>

		$("#default_value").kendoNumericTextBox();
		$("#length_field").kendoNumericTextBox({
			min: 1
		});
		$("#min_value").kendoNumericTextBox();
		$("#max_value").kendoNumericTextBox();

	<?php endif; ?>

	<?php if($field->field_type == 6): ?>
		$("#default_value").kendoDatePicker();
		$("#min_value").kendoDatePicker();
		$("#max_value").kendoDatePicker();
	<?php endif; ?>

	<?php if($field->field_type == 7): ?>
		$("#default_value").kendoDateTimePicker();
		$("#min_value").kendoDateTimePicker();
		$("#max_value").kendoDateTimePicker();
	<?php endif; ?>

	$("#btn-guardar").click(function (){
		enviar(0);  
	});

	<?php if($field->field_type == 6): ?>
		$("#default_value")
	<?php endif; ?>

	
	
	$("#btn-aplicar").click(function (){
		enviar(1);  
	});
	
	$("#btn-cancelar").click(function (){
		location.href = '<?=$url("cms/ContentTypeFieldType/init?CotentType=".$contentType->id)?>';
	});
	
	function enviar(tipo){
		$.post('<?=$url("cms/ContentTypeFieldType/save")?>', $("#form-field").serialize(), function(data){
			if(data == ""){
				if(tipo==0){
					location.href = '<?=$url("cms/ContentTypeFieldType/init?CotentType=".$contentType->id)?>';
				}
				else{
					notification.show({
						title: "!Aviso",
						message: "Guardado"
					}, "success");
				}
			}
			else if(data.error == ""){
				if(tipo==0){
					location.href = '<?=$url("cms/ContentTypeFieldType/init?CotentType=".$contentType->id)?>';
				}
				else{
					notification.show({
						title: "!Aviso",
						message: "Guardado"
					}, "success");
				}
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

	$("#editor").kendoEditor({ resizable: {
		content: true,
		toolbar: true
	}});
	
	$("#field_type").kendoComboBox({
		dataTextField: "name",
		dataValueField: "id",
		dataSource: <?=json_encode($fieldTypes)?>,
		filter: "contains",
		suggest: true
	}).data("kendoComboBox").value(<?=$field->field_type?>);


	$("#num_values").kendoComboBox({
		dataSource: [1,2,3,4,5,6,7,8,9],
		filter: "contains",
		suggest: true
	}).data("kendoComboBox").value(<?=($conf->num_values == 0 ? 1 : $conf->num_values)?>);
	
	
</script>