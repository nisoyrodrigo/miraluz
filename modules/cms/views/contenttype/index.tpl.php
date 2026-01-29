<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : Tipos de Contenido</h1>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	var users = <?=json_encode($users)?>;
	var plantillas = <?=json_encode($templates)?>;
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/contenttype/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/contenttype/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/contenttype/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/contenttype/delete")?>",
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
							template: {type: "number", defaultValue: null },
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
                    title: "Tipo de contenido",
                    width: 250
                },
				{
                	field: "template",
                	title: "Plantilla",
                	values: plantillas,
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
				{ command: ["edit", {name: "campos", text: "Campos", click: editarCampos, class:"k-button-icontext k-grid-edit" }, "destroy"], title: "&nbsp;", width: "300px" }
            ],
            editable: {
				mode:"popup",
			}
		});
	});
	
	function editarCampos(e){
		var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		location.href = '<?=$url("cms/ContentTypeFieldType/init?CotentType=");?>' + dataItem.id;
	}
</script>
