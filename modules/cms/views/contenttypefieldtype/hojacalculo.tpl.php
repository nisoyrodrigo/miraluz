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
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valor por defecto</label>
		</div>
		<div class="col-lg-10">
			<input type="hidden" name="conf[default_value]" id="default_value">
			<div id="spreadsheet" style="width: 100%;"></div>
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
	$("#spreadsheet").kendoSpreadsheet();
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
		var data = $("#spreadsheet").data("kendoSpreadsheet").toJSON();
		$("#default_value").val(JSON.stringify(data, null, 2));
				
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
	
	
</script>