<div class="container">
	<h1><a href="<?=$url("cms")?>">CMS</a> : Usuarios</h1>
	<div class="row">
		<div id="grid-usuarios"></div>
	</div>
</div>
<script>
    var roles = <?=json_encode($roles)?>;

	$(document).ready(function (){

		$("#grid-usuarios").kendoGrid({
            dataSource: {
            	transport: {
            		read: {
                    	url: "http://<?=$burl?>cms/user/getAll",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8",
                    },
                    create: {
                        url: "http://<?=$burl?>cms/user/save",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "http://<?=$burl?>cms/user/save",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "http://<?=$burl?>cms/user/destroy",
						dataType: "jsonp",
                        type: "POST"
					}
                },
                schema: {
                	model: {
                    	id : "id",
                        fields: {   
							id: { type: "number" , editable:false },
                            username: { type: "string" },           
                            password: { type: "string" },
                            rol: {type: "string"}
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
                    field: "username",
                    title: "Usuario",
                    width: 120
                },
                {
                    field: "password",
                    title: "Contrasena"
                },
                {
                    field: "rol",
                    title: "Perfil",
                    width: 200,
                    values: roles
                },
                { command: ["edit", "destroy"], title: "&nbsp;", width: "250px" }
            ],
            editable: "popup"
        });
	});
</script>