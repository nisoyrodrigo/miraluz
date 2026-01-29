<div class="container-fluid">
	<h1><a href="<?=$url("cms")?>">CMS</a> : Roles</h1>
	<div class="row">
		<div class="col-lg-12">
			<div id="grid-roles"></div>
		</div>
	</div>
</div>
<script>
    $(document).ready(function (){

		$("#grid-roles").kendoGrid({
            dataSource: {
            	transport: {
            		read: {
                    	url: "http://<?=$burl?>cms/rol/getAll",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "http://<?=$burl?>cms/rol/save",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "http://<?=$burl?>cms/rol/save",
                        dataType: "jsonp",
                        type: "POST"
                    }
                },
                schema: {
                	model: {
                    	id : "id",
                        fields: {   
							id: { editable: false },
                            name: { type: "string" }
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
					title: "ID"
				},
                {
                    field: "name",
                    title: "Perfil",
                    width: 120
                },
				{ command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }
            ],
            editable: "popup"
        });
	});
</script>