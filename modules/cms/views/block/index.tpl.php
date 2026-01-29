<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : Bloques</h1>
			<a class="k-button" href="javascript:location.href= '<?=$url("cms/block/edit")?>'">Agregar Nuevo Bloque</a><br/><br/>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	var regiones = <?=json_encode($regiones)?>;
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/block/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/block/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/block/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/block/delete")?>",
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
                            name: { type: "string" },
							region: { type: "number" }
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
					field: "ordenamiento",
					title: "Orden",
					encoded: false
				},
                {
                    field: "name",
                    title: "Bloque"
                },
				{
                    field: "region",
                    title: "Region",
					values: regiones
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
		location.href = '<?=$url("cms/block/edit?id=");?>' + dataItem.id;
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
