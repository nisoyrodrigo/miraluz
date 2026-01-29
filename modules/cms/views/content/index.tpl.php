<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : Artículos</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-2">
			<label>Nombre</label><br/>
			<input type="text" class="k-input" id="name" class="filter"/>
		</div>
		<div class="col-lg-2">
			<label>Tipo de contenido</label><br/>
			<select id="content_type" class="filter"></select>
		</div>
		<div class="col-lg-2">
			<label>Publicado</label><br/>
			<select id="published" class="filter"></select>
		</div>
		<div class="col-lg-3">
			<label>&nbsp;</label><br/>
			<button id="filtrar" style="margin-right:20px">Filtrar</button>
			<button id="limpiar" >Limpiar Filtro</button>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
	var users = <?=json_encode($users)?>;
	var types = <?=json_encode($types)?>;
	var templates = <?=json_encode($templates)?>;
	$(document).ready(function (){
		
		$("#content_type").kendoComboBox({
			dataSource : types,
			dataTextField: "text",
            dataValueField: "value"
		});
		
		$("#published").kendoComboBox({
			dataSource : [{"text":"Si", "value": "1"},{"text":"No", "value": "0"}],
			dataTextField: "text",
            dataValueField: "value"
		});
		
		$("#filtrar").kendoButton({
			click:function (e){
				grid.dataSource.filter([
					{
						field: 'content_type',
						operator: ($("#content_type").data("kendoComboBox").value() == "" ? "neq" : "eq"),
						value: $("#content_type").data("kendoComboBox").value()
					},
					{
						field: 'published',
						operator: ($("#published").data("kendoComboBox").value() == "" ? "neq" : "eq"),
						value: $("#published").data("kendoComboBox").value()
					},
					{
						field: 'name',
						operator: ($("#name").val() == "" ? 'neq' : "contains"),
						value: $("#name").val()
					}
				]);
			}
		});
		
		$("#limpiar").kendoButton({
			click:function (e){
				$("#name").val("");
				$("#content_type").data("kendoComboBox").value("");
				$("#published").data("kendoComboBox").value("");
				grid.dataSource.filter({});
			}
		});
		
		$("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/content/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/content/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/content/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/content/delete")?>",
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
							name: { type: "string", validation: { required: true} },
							published: { type: "number" },
							content_type: { type: "number" },
							template: { type: "number" },
							user: { type: "number", editable: false },
							created: { type: "string ", editable: false },
							modified: { type: "string", editable: false }
                        }
                    }
                },
                pageSize: 10
            },
			height: 550,
            sortable: true,
            reorderable: true,
            resizable: true,
			pageable: true,
            toolbar: [ { name: "create", text: "Add", template:'<a class="k-button" href="\\#" onclick="return editar(null)"> + Agregar contenido</a>'}],
            columns: [
				{
					field: "id",
					hidden: true
				},
				{
                    field: "name",
                    title: "Nombre",
                    width: 250
                },
				{
					field: "content_type",
					title: "Tipo de Contenido",
					width: 150,
					values: types
				},
				{
					field: "template",
					title: "Plantilla",
					width: 150,
					values: templates
				},
				{
					field: "published",
					title: "Publicado",
					width: 80,
					values: [{"text":"Si", "value":"1"}, {"text":"No", "value":"0"}]
				},
                {
                	field: "user",
                	title: "Usuario modifico",
                	values: users,
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
				{ command: [{name: "campos", text: "Editar", click: editar, class:"k-button-icontext k-grid-edit" }, "destroy"], title: "&nbsp;", width: 200 }
            ],
            editable: {
				mode:"popup",
			}
		});
		grid = $("#grid").data("kendoGrid");
	});
	
	function editar(e){
		if(e!=null){
			var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
			id = dataItem.id;
			location.href = '<?=$url("cms/content/edit?id=");?>' + id;
		}
		else{
			location.href = '<?=$url("cms/content/selecttype");?>';
		}
		
	}

	function ordena(id, direccion){
		$.get("<?=$url("cms/content/order")?>?id=" + id + "&direction=" + direccion, function (response){

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
