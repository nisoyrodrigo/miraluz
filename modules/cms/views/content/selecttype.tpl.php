<style>
	.k-button{
		margin-right:15px;
		margin-top:20px
	}
	.producto:hover{
		background:#E6E6E6
	}
</style>
<div class="container">
	<div class="row">
		<div class="col-lg-12">
			<h1><a href="<?=$url("cms")?>">CMS</a> : <a href="<?=$url("cms/content")?>">Contenido</a> : Nuevo Contenido</h1>
			<h2>Seleccion tipo de contenido</h2>
		</div>
	</div>
	<div class="row">
		<div id="context-menu"></div>
	</div>
	<div class="row" style="border-top:1px solid #000">
		<div class="col-lg-12"> 
			<a id="btn-guardar" class="k-button">Aceptar</a>
			<a id="btn-cancelar" class="k-button">Cancelar</a>
		</div>
	</div>
</div>
<script type="text/x-kendo-template" id="template">
	<div class="product" style="padding-left:20px">
		<h3>#:name#</h3>
	</div>
</script>
<script>
	var data = <?=json_encode($types)?>;
	var id = 0;
	$(document).ready(function (){
		
		$("#btn-guardar").click(function (){
			if(id == 0){
				alert("Selecciona un tipo de contenido");
			}
			else{
				location.href = '<?=$url("cms/content/edit?type=")?>' + id; 
			}
		});
		
		$("#btn-cancelar").click(function (){
			location.href = '<?=$url("cms/content")?>';
		});
		
		$("#context-menu").kendoListView({
			dataSource: data,
			change: onChange,
			selectable: true,
            template: kendo.template($("#template").html())
		});
		
		function onChange() {
			selected = $.map(this.select(), function(item) {
				return data[$(item).index()].id;
			});
			id = selected;
			console.log("Selected: " + selected.length + " item(s), [" + selected.join(", ") + "]");
		}
	});
</script>
<style>
    #listview-context-menu {
        padding: 0;
        margin-bottom: -1px;
        min-height: 300px;
    }
    .product {
        position: relative;
        height: 62px;
        margin: 0;
        padding: 0;
        border-bottom: 1px solid rgba(128,128,128,.3);
    }
    .product img {
        width: 40px;
        height: 40px;
        border-radius: 40px;
        margin: 10px;
        border: 1px solid #000;
        float: left;
    }
    .product h3 {
        margin: 0;
        padding: 15px 5px 1px 0;
        overflow: hidden;
        line-height: 1em;
        font-size: 1.1em;
        font-weight: bold;
    }
    .product p {
        font-size: .9em;
    }
    .product .date {
        float: right;
        margin: -8px 15px 0 0;
    }
    .k-listview:after {
        content: ".";
        display: block;
        height: 0;
        clear: both;
        visibility: hidden;
    }
    
    @media screen and (max-width: 620px) {
        .product h3 {
           max-width: 100px;
           white-space: nowrap;
           text-overflow: ellipsis;
           height: 15px;
        }
     
    }
</style>