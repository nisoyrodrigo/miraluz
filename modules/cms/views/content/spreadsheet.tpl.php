<?php 
	$conf = new ContentFieldTypeConf("WHERE content_type_field_type = ".$field->id);
	if(empty($content)){
		$value = new stdClass();
	}else{
		$value = new ContentValue("WHERE content = ".$content->id." AND content_type_field_type	= ".$field->id);
	}
	if(empty($value->value_field)){
		$value->value_field = $conf->default_value;
	}
?>
<div class="row">
	<div class="col-lg-12">
		<label><?=$conf->label?></label><br/>
		<input
			type="hidden"
			style="width:100%;height:500px"
			rows="20"
			id="field_<?=$field->id?>"
			name="field_<?=$field->id?>"
			
			/>
		<div id="spreadsheet_<?=$field->id?>" style="width:100%"></div>
		<script>
			$("#spreadsheet_<?=$field->id?>").kendoSpreadsheet({
				change: function (e){
					$("#field_<?=$field->id?>").val(JSON.stringify(e.sender.toJSON(), null, 2));
				}
			});
			var spreedsheet_<?=$field->id?> = $("#spreadsheet_<?=$field->id?>").data("kendoSpreadsheet");
			spreedsheet_<?=$field->id?>.fromJSON(<?=$value->value_field?>);
			$("#field_<?=$field->id?>").val(JSON.stringify(spreedsheet_<?=$field->id?>.toJSON(), null, 2));
		</script>
	</div>
</div>