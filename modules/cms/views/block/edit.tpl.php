<?php
	$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar=array("\\n", "\\n", "\\n", "\\n");
	$block->content = str_replace("'", "\'", $block->content);
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
<form id="form-block">
	<input type="hidden" name="id" value="<?=$block->id?>" action=""/>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/block")?>">Block</a> : Editar
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Nombre del block *</label> 
		</div>
		<div class="col-lg-10">
			<input type="text" class="k-input k-textbox" id="name" name="name" style="width:100%" value="<?=$block->name?>"/>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Region *</label>
		</div>
		<div class="col-lg-10">
			<select id="region" name="region"></select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Tipo de contenido</label>
		</div>
		<div class="12">
			<input type="hidden" id="content_type" name="content_type" value="<?=$block->content_type?>"/>
			<select id="tipo"> </select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<label style="text-align:left">Contenido *</label>
		</div>
		<div class="col-lg-12" id="contentField">
			<input type="hidden" name="content" id="content" value="">
			<textarea id="editor" rows="10" cols="30" style="width:100%;height:400px"><?=str_replace("'","\'",$block->content)?></textarea>
		</div>
	</div>
	<div class="row" style="margin-bottom:0">
		<div class="col-lg-6">
			<select id="paginas" name="iquals_show_in" style="width:100%"></select>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6">
			<textarea class="k-input" id="show_in" name="show_in" rows="5" cols="30" style="width:100%"><?=$block->show_in?></textarea>
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

	<?php if($block->content_type == 1): ?>
		$("#editor").kendoEditor({ 
			resizable: {
				content: true
			},
			tools: [
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
			]
		});
   	<?php endif; ?>

	$("#btn-guardar").click(function (){
		enviar(0);  
	});
	
	$("#btn-aplicar").click(function (){
		enviar(1);  
	});
	
	$("#btn-cancelar").click(function (){
		location.href = '<?=$url("cms/block")?>';
	});
	
	function enviar(tipo){
		$("#content").val(editAreaLoader.getValue("editor"));
		$.post('<?=$url("cms/block/save")?>', $("#form-block").serialize(), function(data){
			if(data.error != ""){
				notification.show({
					title: "!Aviso",
					message: data.error
				}, "error");
			}
			else{
				if(tipo==0){
					location.href = '<?=$url("cms/block")?>';
				}
				else{
					notification.show({
						title: "!Aviso",
						message: "Guardado"
					}, "success");
				}
			}
			
		});
	}

	
	
	$("#region").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: <?=json_encode($regiones)?>,
		filter: "contains",
		suggest: true
	}).data("kendoComboBox").value(<?=$block->region?>);

	$("#tipo").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: [{"text":"Codigo abierto" , "value":"0"},{"text":"Full HTML", "value": "1"}, {"text":"Archivo", "value": "2"}],
		filter: "contains",
		suggest: true,
		change: function (e){
			$("#content_type").val($("#tipo").val());
			var content = $("#editor").val();
			
			$("#contentField").html('<textarea id="editor" name="content" rows="10" cols="30" style="width:100%;height:400px"></textarea>');

			$("#editor").val(content);
			
			if($("#tipo").val()==1){
				$("#editor").kendoEditor({ 
					resizable: {
						content: true
					},
					tools: [
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
					]
				});
			}
			else{
				editAreaLoader.setValue("editor", '<?=str_ireplace($buscar,$reemplazar,$block->content)?>');
			}
		}
	}).data("kendoComboBox").value(<?=$block->content_type?>);
	
	$("#paginas").kendoComboBox({
		dataTextField: "text",
		dataValueField: "value",
		dataSource: [{"text":"Todas las páginas excepto las que se enumeran" , "value":"0"},{"text":"Sólo las páginas enumeradas", "value": "1"}],
		suggest: true
	}).data("kendoComboBox").value(<?=$block->iquals_show_in?>);
</script>