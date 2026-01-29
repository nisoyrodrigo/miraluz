<?php
	if(empty($conf->allowed_values)){
		$textoPositivo = "Verdadero";
		$textoNegativo = "Falso";
		$conf->allowed_values = "1|Verdadero\n0|Falso";
	}
	else{
		$textos = explode("\n", $conf->allowed_values);
		$textoPositivo = explode("|", $textos[0])[1];
		$textoNegativo = explode("|", $textos[1])[1];
	}
?>
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
			<input type="text" class="k-input k-textbox" id="name" name="field[name]" style="width:100%" value="<?=$field->name?>" required/>
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
			<input type="text" class="k-input k-textbox" id="label" name="conf[label]" value="<?=$conf->label?>" required data-required-msg="Label es requerido"/>
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
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Texto Positivo </label>
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="textopositivo" name="TextoPositivo" value="<?=$textoPositivo?>" required/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Texto Negativo </label>
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="textonegativo" name="TextoNegativo" value="<?=$textoNegativo?>" required/>
		</div>
	</div>
	<div class="row" style="display:none">
		<div class="col-lg-2">
			<label style="text-align:left">Valores permitidos (value|text)</label>
		</div>
		<div class="col-lg-10">
			<textarea rows="10" cols="100" style="width:500px" name="conf[allowed_values]" id="allowed_values"><?=$conf->allowed_values?></textarea>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Numero de valores</label>
		</div>
		<div class="col-lg-10">
			<select id="num_values" name="conf[num_values]"></select>
		</div>
	</div>
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
	$("#default_value").kendoEditor();
	$("#allowed_values").addClass("k-textBox");
	$("#default_value").addClass("k-textBox");
	$("#length_field").addClass("k-textBox");

	$("#btn-guardar").click(function (){
		enviar(0);  
	});

	$("#btn-aplicar").click(function (){
		enviar(1);  
	});
	
	$("#btn-cancelar").click(function (){
		location.href = '<?=$url("cms/ContentTypeFieldType/init?CotentType=".$contentType->id)?>';
	});
	
	function enviar(tipo){
		var validator = $("#form-field").kendoValidator().data("kendoValidator");
		if(validator.validate()){
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
		dataSource: [
			{"value":0, "text":"Sin Limite"},
			{"value":1, "text": "1"},
			{"value":2, "text": "2"},
			{"value":3, "text": "3"},
			{"value":4, "text": "4"},
			{"value":5, "text": "5"},
			{"value":6, "text": "6"},
			{"value":7, "text": "7"},
			{"value":8, "text": "8"},
			{"value":9, "text": "9"},
			{"value":10, "text": "10"}
		],
		dataTextField: "text",
		dataValueField: "value",
		defaultValue: 1,
		filter: "contains",
		suggest: true
	}).data("kendoComboBox").value(<?=($conf->num_values == 0 ? 1 : $conf->num_values)?>);

	$("#textopositivo").change(function (){
		$("#allowed_values").html('1|' + $("#textopositivo").val() + '\n' + '0|' + $("#textonegativo").val());
	});

	$("#textonegativo").change(function (){
		$("#allowed_values").html('1|' + $("#textopositivo").val() + '\n' + '0|' + $("#textonegativo").val());
	});
	
	
</script>