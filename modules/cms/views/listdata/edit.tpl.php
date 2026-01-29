<?php
	$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar=array("\\n", "\\n", "\\n", "\\n");
?>
<script language="Javascript" type="text/javascript" src="<?=$urlm("js/edit_area/edit_area_full.js")?>"></script>
<script language="Javascript" type="text/javascript">
		// initialisation
		editAreaLoader.init({
			id: "query_editor"	// id of the textarea to transform		
			,start_highlight: true	// if start with highlight
			,allow_resize: "both"
			,word_wrap: true
			,language: "en"
			,syntax: "sql"	
		});

		editAreaLoader.init({
			id: "template_editor"	// id of the textarea to transform		
			,start_highlight: true	// if start with highlight
			,allow_resize: "both"
			,word_wrap: true
			,language: "en"
			,syntax: "html"	
		});
</script>
<style>
	.k-button{
		margin-right:15px;
		margin-top:20px
	}
</style>
<form id="form-listdata">
	<input type="hidden" name="id" id="id" value="<?=$model->id?>" action=""/>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/listdata")?>">List Data</a> : Editar
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Nombre del List Data *</label> 
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" required id="name" name="name" style="width:100%" value="<?=$model->name?>"/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Tipo</label>
		</div>
		<div class="col-lg-10">
			<select id="type" name="type" required> </select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Tipo de contenido</label>
		</div>
		<div class="col-lg-10">
			<select id="content_type" name="content_type" required> </select>
		</div>
	</div>
	<div id="paraFile" style="display:none">
		<div class="row">
			<div class="col-lg-2">
				<label>Archivo *</label>
			</div>
			<div class="col-lg-10">
				<input type="text" class="k-input k-textbox" id="file" name="file" value="<?=$model->file?>" >
			</div>
		</div>
	</div>
	<div id="paraQuery">
		<div class="row">
			<div class="col-lg-12">
				<label style="text-align:left">Query *</label>
			</div>
			<div class="col-lg-12" id="contentField">
				<input type="hidden" name="query" id="query" value="">
				<textarea id="query_editor" name="query_editor" rows="10" cols="30" style="width:100%;height:400px"></textarea>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<label style="text-align:left">Template </label>
			</div>
			<div class="col-lg-12" id="contentField">
				<input type="hidden" name="template" id="template" value="">
				<textarea id="template_editor" name="template_editor" rows="10" cols="30" style="width:100%;height:400px"></textarea>
			</div>
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
	var validator = $("#form-listdata").kendoValidator().data("kendoValidator");

	$(document).ready(function (){
		if($("#type").val() == 0){
			$("#file").prop("required", false);
			$("#query").prop("required", true);
			$("#paraQuery").css({"display":"block"});
			$("#paraFile").css({"display":"none"});
		}
		else{
			$("#file").prop("required", true);
			$("#query").prop("required", false);
			$("#paraQuery").css({"display":"none"});
			$("#paraFile").css({"display":"block"});
		}
		editAreaLoader.setValue("query_editor", "<?=str_ireplace($buscar,$reemplazar,$model->query)?>");
		editAreaLoader.setValue("template_editor", '<?=str_ireplace($buscar,$reemplazar,$model->template)?>');
	});

	$("#btn-guardar").click(function (){
		enviar(0);  
	});
	
	$("#btn-aplicar").click(function (){
		enviar(1);  
	});
	
	$("#btn-cancelar").click(function (){
		location.href = '<?=$url("cms/listdata")?>';
	});
	
	function enviar(tipo){
		$("#query").val(editAreaLoader.getValue("query_editor"));
		$("#template").val(editAreaLoader.getValue("template_editor"));
		if (validator.validate()) {
			$.post('<?=$url("cms/listdata/save")?>', $("#form-listdata").serialize(), function(data){
				if(data.error == undefined){
					if(data != ""){
						notification.show({
							title: "!Aviso",
							message: data
						}, "error");
					}
				}
				else if(data.error != ""){
					notification.show({
						title: "!Aviso",
						message: data.error
					}, "error");
				}
				else{
					if(tipo==0){
						location.href = '<?=$url("cms/listdata")?>';
					}
					else{
						notification.show({
							title: "!Aviso",
							message: "Guardado"
						}, "success");
					}
					$("#id").val(data.id);
				}
				
			});
		}
		else{
			notification.show({
				title: "!Aviso",
				message: "Los campos marcados con * son obligatorios"
			}, "error");
		}
	}

	$("#type").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: [{"value":"0", "text":"Query"},{"value":"1", "text": "archivo"}],
		filter: "contains",
		suggest: true,
		defaultValue: 0,
		change: function (e){
			if($("#type").val() == 0){
				$("#file").prop("required", false);
				$("#query").prop("required", true);
				$("#paraQuery").css({"display":"block"});
				$("#paraFile").css({"display":"none"});
			}
			else{
				$("#file").prop("required", true);
				$("#query").prop("required", false);
				$("#paraQuery").css({"display":"none"});
				$("#paraFile").css({"display":"block"});
			}
		}
	}).data("kendoComboBox").value(<?=($model->type==null ? 0 : $model->type)?>);

	$("#content_type").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: <?=json_encode($content_types)?>,
		filter: "contains",
		suggest: true,
		defaultValue: 0
	}).data("kendoComboBox").value(<?=($model->content_type)?>);
</script>