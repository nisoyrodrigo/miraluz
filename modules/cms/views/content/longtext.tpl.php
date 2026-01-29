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
		<textarea 
			style="width:100%;height:500px"
			rows="20"
			id="field_<?=$field->id?>"
			name="field_<?=$field->id?>"
			data-required-msg="<?=$field->name." es requerido"?>"
			<?=($conf->required == 1 ? "required" : "")?>><?=$value->value_field?></textarea>
		<script>
			$("#field_<?=$field->id?>").kendoEditor({tools: [
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
			]});
		</script>
	</div>
</div>