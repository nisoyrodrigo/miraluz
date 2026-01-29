<?php
	$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar=array("\\n", "\\n", "\\n", "\\n");
?>
<script language="Javascript" type="text/javascript" src="<?=$urlm("js/edit_area/edit_area_full.js")?>"></script>
<script language="Javascript" type="text/javascript">
		// initialisation
		editAreaLoader.init({
			id: "editor"	// id of the textarea to transform		
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
<form id="form-template">
	<input type="hidden" name="id" id="id" value="<?=$template->id?>"/>
	<input type="hidden" name="content" id="content" value=""/>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/template")?>">Plantillas</a> : Editar
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Nombre de la plantilla *</label> 
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="name" name="name" style="width:100%" value="<?=$template->name?>"/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Tipo de plantilla</label>
		</div>
		<div class="12">
			<input type="hidden" id="template_type" name="template_type" value="<?=$template->template_type?>"/>
			<select id="tipo"> </select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<label style="text-align:left">Contenido *</label>
			Los archivos de plantilla deben de crearse en la ruta: <b><?=Motor::app()->absolute_url?>templates/example.tpl.php</b>
		</div>
		<div class="col-lg-12" id="div-editor">
			<textarea id="editor" rows="10" cols="30" style="width:100%;height:400px"><?=$template->content?></textarea>
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

	$(document).ready(function (){
		editAreaLoader.setValue("editor", '<?=str_ireplace($buscar,$reemplazar,$template->content)?>');
	});

	$("#btn-guardar").click(function (){
		enviar(0);  
	});
	
	$("#btn-aplicar").click(function (){
		enviar(1);  
	});
	
	$("#btn-cancelar").click(function (){
		location.href = '<?=$url("cms/template")?>';
	});
	
	function enviar(tipo){
		$("#content").val(editAreaLoader.getValue("editor"));
		$.post('<?=$url("cms/template/save")?>', $("#form-template").serialize(), function(data){
			if(data.error == undefined){
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
			else{
				if(tipo==0){
					location.href = '<?=$url("cms/template")?>';
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

	$("#tipo").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: [{"text":"Codigo" , "value":"0"},{"text":"Archivo", "value": "1"}],
		filter: "contains",
		suggest: true,
		change: function (e){
			$("#template_type").val($("#tipo").data("kendoComboBox").value());
		}
	}).data("kendoComboBox").value(<?=$template->template_type?>);
</script>