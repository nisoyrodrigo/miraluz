<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1> <a href="<?=$url("cms")?>">CMS</a> : Regiones
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
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
                    	url: "<?=$url("cms/region/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/region/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/region/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/region/delete")?>",
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
                            name: { type: "string", validation: {required: true} }
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
					title: "ID",
					width:100
				},
                {
                    field: "name",
                    title: "Region"
                },
				{ command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }
            ],
            editable: "popup"
		});
	});
</script>