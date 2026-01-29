<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/contenttype")?>"/>Tipos de Contenido</a> : <?=$contentType->name?> : Editar campos</h1>
			<h4><b>* Todos los tipos de contenido tienen por default el campo Título</b></h4>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	var fieldTypes = <?=json_encode($fieldTypes)?>;
	var users = <?=json_encode($users)?>;
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/ContentTypeFieldType/getAll?ContentType=".$contentType->id)?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/ContentTypeFieldType/save?content_type=".$contentType->id)?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/ContentTypeFieldType/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/ContentTypeFieldType/delete")?>",
						dataType: "jsonp",
                        type: "POST"
					}
                },
				requestStart: function (e){
					accion = e.type;
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
							name: { type: "string" },
							field_type: { type: "number" },
							user: { type: "number", editable: false },
							created: { type: "string ", editable: false },
							modified: { type: "string", editable: false },
							fecha_modificacion: { type: "number" }
                        }
                    }
                },
                pageSize: 15
            },
			height: 550,
            sortable: true,
            reorderable: true,
            resizable: true,
            pageable: true,
            toolbar: ["create"],
            columns: [
				{
					field: "id",
					hidden: true
				},
				{
                    field: "name",
                    title: "Nombre del campo",
                    width: 250
                },
                {
                	field: "field_type",
                	title: "Tipo de campo",
                	values: fieldTypes,
                	width: 150
                },
                {
                	field: "user",
                	title: "Usuario modifico",
                	values: users,
                	width: 150
                },
				{
                    field: "created",
                    title: "Creado",
                    width: 150
                },
                {
                    field: "modified",
                    title: "Modificado",
                    width: 150
                },
				{ command: [{name: "editarCampo", text: "Editar", click: editarCampo, class:"k-button-icontext k-grid-edit" },  "destroy"], title: "&nbsp;", width: "300px" }
            ],
            editable: {
				mode:"popup",
			}
		});
	});
	
	function editarCampo(e){
		var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		location.href = '<?=$url("cms/ContentTypeFieldType/edit&id=");?>' + dataItem.id;
	}

	function ordena(id, direccion){
		$.get("<?=$url("cms/block/order")?>?id=" + id + "&direction=" + direccion, function (response){

			var result = response.error == undefined ? response : response.error;  


			if(result != ""){
				notification.show({
					title: "!Aviso",
					message: result
				}, "error");
			}
			else{
				$("#grid").data("kendoGrid").dataSource.read();
			}

			$("#grid").data("kendoGrid").dataSource.read();
		});
	}
</script>
