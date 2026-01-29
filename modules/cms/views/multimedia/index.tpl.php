<div class="container-fluid">
	<div class="row">
		<div class="col-lg-12">
			<h1>Multimedia</h1>
			<div id="grid"></div>
		</div>
	</div>
</div>
<script>
    function filterAutoCompleteDataSource(e) {
      var gridFilter = e.sender.dataSource.filter();
      e.sender.element.find(".k-autocomplete input").data("kendoAutoComplete").dataSource.filter(gridFilter);
    }
	$(document).ready(function (){
		var grid = $("#grid").kendoGrid({
			dataSource: {
            	transport: {
            		read: {
                    	url: "<?=$url("cms/multimedia/getAll")?>",
                    	type: "POST",
                		dataType: "jsonp",
                		contentType: "application/json; charset=utf-8"
                    },
                    create: {
                        url: "<?=$url("cms/multimedia/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					update: {
                        url: "<?=$url("cms/multimedia/save")?>",
                        dataType: "jsonp",
                        type: "POST"
                    },
					destroy: {
						url: "<?=$url("cms/multimedia/delete")?>",
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
							id_aux: { type: "number", editable: true },
                            name: { type: "string", validation: {required: true} },
                            type: { type: "number", validation: {required: true} },
                            url: { type: "string", editable: true },
                            archivo: { type: "file" }
                        }
                    }
                },
                pageSize: 15
            },
			height: 400,
            sortable: true,
            reorderable: true,
            resizable: true,
            pageable: true,
            toolbar: ["create"],
            dataBound : filterAutoCompleteDataSource,
            filterable : {
                mode : "row"
            },
            columns: [
				{
					field: "id",
					hidden:true
				},
				{
					field: "id_aux",
					hidden:true
				},
                {
                    field: "name",
                    title: "Nombre",
                    width:200,
                    filterable : {
                      cell : {
                        operator : "contains"
                      }
                    }
                },
                {
                	editor: fileEditor,
                	field: "archivo",
                	hidden:true,
                	filterable: false
                },
                {
                	field: "url",
                	hidden:true,
                	filterable: false
                },
                {
                    field: "type",
                    title: "Tipo",
                    values: <?=json_encode($types)?>,
                    width:150,
                    filterable : {
                      cell : {
                        showOperators : false
                      }
                    }
                },
				{ command: ["edit",{name: "copiar", text: "&#60;/&#62;", click: copiarPortapapeles, class:"k-button-icontext k-grid-edit" }, "destroy"], title: "&nbsp;", width: "200px" }
            ],
            editable: "popup"
		}).data("kendoGrid");

        
	});
	
	function copiarPortapapeles(e){
		$("body").append("<input type='text' id='temp'>"); // Acá se crea un input dinamicamente con un id para luego asignarle un valor sombreado
        var dataItem = this.dataItem($(e.currentTarget).closest("tr"));
		$("#temp").val(dataItem.url).select(); // Acá se obtiene el id del boton que hemos creado antes y se le agrega un valor y luego se le sombrea con select(). Para agregar lo que se quiere copiar editas val("EDITAESTOAQUÍ")
        document.execCommand("copy"); // document.execCommand("copy") manda a copiar el texto seleccionado en el documento
        $("#temp").remove();
	}

	function fileEditor(container, options)
    {
        $('<input type="file" name="archivo"/>')
            .appendTo(container)
            .kendoUpload({
                multiple: false,
                async: {
                    saveUrl: "<?=$url("cms/multimedia/savefile")?>",
                    autoUpload: true
                },
                upload: function (e) {
                    e.data = { id: options.model.id};
                },
                success: function (e) {
					options.model.set("id_aux", e.response.id);
                    options.model.set("url", e.response.url);
                }
        });
    }
</script>