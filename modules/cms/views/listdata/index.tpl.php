<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : List Data</h1>
			<a class="k-button" href="javascript:location.href= '<?=$url("cms/listdata/edit")?>'">Agregar Nuevo List Data</a><br/><br/>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	var users = <?=json_encode($users)?>;
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/listdata/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/listdata/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/listdata/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/listdata/delete")?>",
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
							ordenamiento : { type: "string" },
                            type: { type: "number" },
							user: { type: "number" }
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
                    title: "Bloque"
                },
                {
                    field: "type",
                    title: "Tipo",
                    values: [{"value":"0","text":"Codigo abierto"},{"value":"1", "text": "Archivo"}]
                },
				{
                	field: "user",
                	title: "Usuario modifico",
                	values: users,
					filterable: false, 
                	width: 100
                },
				{
                    field: "created",
                    title: "Creado",
                    width: 140
                },
                {
                    field: "modified",
                    title: "Modificado",
                    width: 140
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
		location.href = '<?=$url("cms/listdata/edit?id=");?>' + dataItem.id;
	}

</script>
