<div class="container-fluid">
	<h1><a href="<?=$url("cms")?>">CMS</a> : Permisos</h1>
	<div class="row">
		<div class="col-lg-12">
			<div id="grid-permisos"></div>
		</div>
	</div>
</div>
<script>
    var sections = <?=json_encode($sections)?>;
    var users = <?=json_encode($users)?>;
    var rols = <?=json_encode($rols)?>;
    var permisos = [{"value": 0, "text": "No"}, {"value":1, "text": "Si"}];
    $(document).ready(function (){

		$("#grid-permisos").kendoGrid({
            dataSource: {
            	transport: {
            		read: {
                    	url: "http://<?=$burl?>cms/permiso/getAll",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "http://<?=$burl?>cms/permiso/save",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "http://<?=$burl?>cms/permiso/save",
                        dataType: "jsonp",
                        type: "POST"
                    }
                },
                schema: {
                	model: {
                    	id : "id",
                        fields: {   
							id: { editable: false },
                            section: { type: "number"},
                            user: { type: "number"},
                            rol: { type: "number"},
                            permiso: { type: "number"}
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
                    field: "section",
                    title: "Seccion",
                    values: sections
                },
                {
                    field: "user",
                    title: "Usuario",
                    values: users
                },
                {
                    field: "rol",
                    title: "Rol",
                    values: rols
                },
                {
                    field: "permiso",
                    title: "Permiso",
                    values: permisos
                },
				{ command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }
            ],
            editable: "popup"
        });
	});
</script>