<style>
	.k-button{
		margin-right:15px;
		margin-top:20px
	}
</style>
<form id="form-field">
	<input type="hidden" name="field[id]" id="id_field" value="<?=$field->id?>"/>
	<input type="hidden" name="conf[id]" id="id_conf" value="<?=$conf->id?>"/>
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
			<label style="text-align:left">Archivo por defecto</label>
		</div>
		<div class="col-lg-8" id="divFile">
			<input type="file" name="default_value_file" id="default_value_file">
		</div>
		<div class="col-lg-2" >
			<div id="videoVisible" style="<?=$conf->default_value!=""?$conf->default_value:"display:none"?>">
				<center>
					<video autoplay loop muted preload="metadata" playsinline id="video-por-defecto" height="100px">
                        <source src="<?=$conf->default_value?>" type="video/multiple"></source>
                    </video>
					<!--<img src="<?=$conf->default_value?>" id="imagen-por-defecto" height="100px" >-->
					<a href="javascript:eliminarVideo()" class="k-button">Eliminar</a>
				</center>
			</div>
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
	<div class="row">
		<div class="col-lg-2">
			<label style="text-align:left">Valores permitidos</label>
		</div>
		<div class="col-lg-10">
			<textarea rows="10" cols="20" name="conf[allowed_values]" id="allowed_values"><?=$conf->allowed_values?></textarea>
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

	cargaControlArchivo();

	function eliminarImagen(){
		if(confirm("¿Deseas eliminar el archivo por defecto?")){
			$.get("<?=$url("cms/contenttypefieldtype/deletefile?id_field=".$field->id."&id_conf=")?>"+$("#id_conf").val(), function(response){
				if(response.error == ""){
					$("#divFile").html('<input type="file" name="default_value_file" id="default_value_file">');
					$("#imagenVisible").css("display", "none");
					$("#imagen-por-defecto").find("source").attr("src", "");
					cargaControlArchivo();
				}
				else{
					notification.show({
						title: "!Aviso",
						message: response.error
					}, "error");
				}
				
			});
		}
	}
	
	function cargaControlArchivo(){
		$("#default_value_file").kendoUpload({
			multiple: false,
            validation: {
                allowedExtensions: [".mp4", ".avi", ".webm"]
            },
			async: {
				saveUrl: "<?=$url("cms/contenttypefieldtype/savefile?id_field=".$field->id."&id_conf=".$conf->id)?>",
				autoUpload: true
			},
			success: function (e) {
				$("#conf_id").val(e.response.id_conf);
				$("#imagenVisible").css("display", "block");
				$("#imagen-por-defecto").find("source").attr("src", e.response.default_value);
			}
		});
	}

	$("#allowed_values").addClass("k-textBox");

	
	
	$("#allowed_values").addClass("k-textBox");
	
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
	}).data("kendoComboBox").value(<?=$conf->num_values?>);
	
	
</script>