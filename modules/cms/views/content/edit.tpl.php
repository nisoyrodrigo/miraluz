<script>
var accion = null;
function eliminarImagen(id){
		if(confirm("¿Deseas eliminar el archivo?")){
			var content_id = $("#id").val();
			$.get("<?=$url("cms/content/deletefile?content=")?>" + content_id + "&id_field=" + id, function(response){
				if(response == ""){
					$("#file_" + id).data("kendoUpload").removeAllFiles();
					$("#imagenVisible" + id).css("display", "none");
					$("#imagen-por-defecto" + id).attr("src", "");
				}
				else if(response.error != undefined){
					if(response.error == ""){
						$("#file_" + id).data("kendoUpload").removeAllFiles();
						$("#imagenVisible" + id).css("display", "none");
						$("#imagen-por-defecto" + id).attr("src", "");
					}
					else{
						notification.show({
							title: "!Aviso",
							message: response
						}, "error");
					}
				}
				else{
					notification.show({
						title: "!Aviso",
						message: response
					}, "error");
				}
				
			});
		}
	}

function eliminarVideo(id){
        if(confirm("¿Deseas eliminar el archivo?")){
            $.get("<?=$url("cms/content/deletefile?content=".$content->id."&id_field=")?>" + id, function(response){
                if(response == ""){
                    $("#file_" + id).data("kendoUpload").removeAllFiles();
                    $("#videoVisible" + id).css("display", "none");
                    $("#video-por-defecto" + id).attr("src", "");
                }
                else if(response.error != undefined){
                    if(response.error == ""){
                		$("#file_" + id).data("kendoUpload").removeAllFiles();
	                    $("#videoVisible" + id).css("display", "none");
	                    $("#video-por-defecto" + id).attr("src", "");
                    }
                    else{
                        notification.show({
                            title: "!Aviso",
                            message: response
                        }, "error");
                    }
                }
                else{
                    notification.show({
                        title: "!Aviso",
                        message: response
                    }, "error");
                }
                
            });
        }
    }
</script>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/content")?>">Contenido</a> : Editar
		</div>
	</div>
	<form id="form-content">
		<input type="hidden" name="id" id="id" value="<?=$content->id?>"/>
		<input type="hidden" name="content_type" value="<?=$content->content_type?>"/> 
		<div class="row">
			<div class="col-lg-12">
				<label><h3>Titulo</h3></label>
			</div>
			<div class="col-lg-12">
				<input 
					size="50"
					style="width:100%"
					type="text" 
					name="name" 
					id="name" 
					class="k-input k-textbox"
					value="<?=($content->name=="temp"?"":$content->name)?>"
					required
					/>
			</div>
		</div>
	<?php 
		$path = Motor::app()->absolute_url.$murl;
		foreach($fields as $field){
			switch($field->field_type){
				case '1'://Text
					include $path."/views/content/text.tpl.php";
					break;
				case '2'://LongText
					include $path."/views/content/longtext.tpl.php";
					break;
				case '3'://Number 
					include $path."/views/content/number.tpl.php";
					break;
				case '4'://Decimal 
					include $path."/views/content/decimal.tpl.php";
					break;
				case '6'://ComboBox
					include $path."/views/content/combobox.tpl.php";
					break;
				case '7'://Imagen
					include $path."/views/content/file.tpl.php";
					break;
				case '8'://Imagen
					include $path."/views/content/imagen.tpl.php";
					break;
				case '9'://Date
					include $path."/views/content/date.tpl.php";
					break;
				case '10'://DateTime
					include $path."/views/content/datetime.tpl.php";
					break;
				case '11'://Bool
					include $path."/views/content/bool.tpl.php";
					break;
				case '12'://Email 
					include $path."/views/content/email.tpl.php";
					break;
                case '13'://Video 
					include $path."/views/content/video.tpl.php";
                    break;
				case '14'://Query
					include $path."/views/content/query.tpl.php";
					break;
				case '15'://Spreadsheet
					include $path."/views/content/spreadsheet.tpl.php";
					break;
			}
		} 	
	?>
		<div class="row" style="border-top:1px solid #000">
			<div col-lg-12>
				<h1>Configuración</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Publicar</label>
			</div>
			<div class="col-lg-10">
				<select id="published" name="published"></select>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Template</label>
			</div>
			<div class="col-lg-10">
				<select id="template" name="template"></select>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-2">
				<label>Url</label>
			</div>
			<div class="col-lg-10">
				<input 
						size="200"
						style="width:100%"
						type="text" 
						name="url" 
						id="url" 
						class="k-input k-textbox"
						value="<?=$content->url?>"
						/>
			</div>
		</div>
		<div class="row" style="border-top:1px solid #000;padding-top:15px">
			<div class="col-lg-12"> 
				<a id="btn-guardar" class="k-button">Guardar</a>
				<a id="btn-aplicar" class="k-button">aplicar</a>
				<a id="btn-cancelar" class="k-button">Cancelar</a>
			</div>
		</div>
	</form>
</div>
<script>
	var validator = $("#form-content").kendoValidator().data("kendoValidator");

	$(document).ready(function (){
		$("#published").kendoComboBox({
			dataTextField: "text",
			dataValueField: "value",
			dataSource: [{"text":"Si", "value":"1"},{"text":"No", "value":"0"}],
			filter: "contains",
			suggest: true
		}).data("kendoComboBox").value("<?=$content->published?>");
		
		$("#template").kendoComboBox({
			dataTextField: "text",
			dataValueField: "value",
			dataSource: <?=json_encode($templates)?>,
			filter: "contains",
			suggest: true
		}).data("kendoComboBox").value("<?=(empty($content->id) ? $contentType->template : $content->template)?>");
		
		$("#btn-guardar").click(function (){
			enviar(0);  
		});
		
		$("#btn-aplicar").click(function (){
			enviar(1);  
		});
		
		$("#btn-cancelar").click(function (){
			location.href = '<?=$url("cms/Content")?>';
		});
		
		
	});

	function enviar(tipo){
		if (validator.validate()) {

			$.post('<?=$url("cms/content/save")?>', $("#form-content").serialize(), function(data){
				if(data == ""){
					if(tipo==0){
						location.href = '<?=$url("cms/content")?>';
					}
					else{
						notification.show({
							title: "!Aviso",
							message: "Guardado"
						}, "success");
					}
					$("#id").val(data.id);
				}
				else if(data.error == ""){
					if(tipo==0){
						location.href = '<?=$url("cms/content")?>';
					}
					else{
						notification.show({
							title: "!Aviso",
							message: "Guardado"
						}, "success");
					}
					$("#id").val(data.id);
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
</script>