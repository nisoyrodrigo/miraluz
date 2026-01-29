<?php 
	$conf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
	$values = array();
	if($conf->num_values == 1){
		if(empty($content)){
			$value = new stdClass();
		}else{
			$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
		}
	}
	else{
		$values = ContentValue::model()->findAll("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
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
			<input type="file" id="file_<?=$field->id?>" name="file_<?=$field->id?>">
			<div id="grid<?=$field->id?>"></div>
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
								url: "<?=$url("cms/content/deleteFile")?>",
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
							template: '<center>' +
							'<video preload="metadata"  height="100px" controls>' +
                            '<source src="<?=$url()?>#: data.value_field#"' +
                   			'</video></center>',
							width: 250
						},
						{ command: [{name: "destroy", text: "Eliminar"}], title: "&nbsp;", width: 200 }
					],
					editable: "inline",
				});
				$("#file_<?=$field->id?>").kendoUpload({
					multiple: false,
					async: {
						saveUrl: "<?=$url("cms/content/savefile?content=".$content->id."&id_field=".$field->id."&content_type=".$contentType->id)?>",
						autoUpload: true
					},
					success: function (e) {
						$("#id").val(e.response.content);
						$("#grid<?=$field->id?>").data("kendoGrid").dataSource.transport.options.read.url = "<?=$url("cms/content/getValueForField?field=".$field->id."&content=")?>" + response.content;
						$("#grid<?=$field->id?>").data("kendoGrid").dataSource.read();
						$("#file_<?=$field->id?>").data("kendoUpload").removeAllFiles();
					}
				});
			</script>
		<?php else: ?>
			<div style="width:60%;display:inline-block;margin-bottom:15px" id="divVideo<?=$field->id?>">
				<input type="file" id="file_<?=$field->id?>" name="file_<?=$field->id?>_1">
			</div>
			<div id="videoVisible<?=$field->id?>" style="<?=empty($value->value_field) ? ( empty($conf->default_value) ? "display:none" : "") : ""?>">
                <center>
                    <video preload="metadata" id="imagen-por-defecto" height="100px" controls>
                        <source src="<?=$url($value->value_field)?>">
                    </video>
                    <a href="javascript:eliminarVideo(<?=$field->id?>)" class="k-button">Eliminar</a>
                </center>
            </div>
			<script>
				$("#file_<?=$field->id?>").kendoUpload({
					multiple: false,
					async: {
						saveUrl: "<?=$url("cms/content/savefile?content=".$content->id."&id_field=".$field->id."_1&content_type=".$contentType->id)?>",
						autoUpload: true
					},
					success: function (e) {
						$("#id").val(e.response.content);
						$("#videoVisible<?=$field->id?>").css("display", "inline-block");
						$("#video-por-defecto<?=$field->id?>").attr("src", "<?=$url()?>" + e.response.value_field);
					}
				});
			</script>
		<?php endif; ?>
		<?php if($conf->num_values != 1): ?>
		</fieldset>
		<?php endif; ?>

	</div>
</div>