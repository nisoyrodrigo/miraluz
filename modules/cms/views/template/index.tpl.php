<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : Templates</h1>
			<a class="k-button" href="javascript:location.href= '<?=$url("cms/template/edit")?>'">Agregar Nuevo Template</a><br/><br/>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/template/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/template/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/template/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/template/delete")?>",
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
							tipo: { type: "number" }
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
            columns: [
				{
					field: "id",
					hidden: true,
					width: 100
				},
				{
                    field: "name",
                    title: "Template"
                },
				{
                    field: "template_type",
                    title: "Tipo template",
					values: [{"text":"Código", "value":"0"}, {"text":"Archivo", value:"1"}]
                },
				{ command: [{name: "commandName", text: "Editar", click: editar }, "destroy"], title: "&nbsp;", width: "250px" }
            ],
            editable: {
				mode:"popup",
			}
		});
	});
	
	function editar(e){
		var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		location.href = '<?=$url("cms/template/edit?id=");?>' + dataItem.id;
	}
</script>
