<!DOCTYPE html>
<html>
	<head>
		<title>Administrador</title>
	</head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<link rel="stylesheet" href="<?=$urlm("css/cms_template.css")?>">
	<link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
	
	<!-- Bootstrap -->
	<link rel="stylesheet" href="<?=$url("css/bootstrap.min.css")?>">

	<!-- Kendo -->
	
	<link rel="stylesheet" href="<?=$url("css/kendo/kendo.common.min.css")?>">
	<link rel="stylesheet" href="<?=$url("css/kendo/kendo.default.min.css")?>">

	<!-- jQuery -->
	<script src="<?=$url("js/jquery.js")?>"></script>
	<script src="<?=$url("js/bootstrap.min.js")?>"></script>
	<script src="<?=$url("js/kendo/kendo.all.min.js")?>"></script>
</html>

<style>
	table {
		white-space: normal;
		line-height: normal;
		font-weight: normal;
		font-size: 12px;
		font-style: normal;
		color: -internal-quirk-inherit;
		text-align: start;
		font-variant: normal normal;
	}
	
	fieldset{
		border: 1px solid silver;
		padding:20px;
		border-radius:5px
	}
	
	legend{
		width: initial;
		padding-top:0;
		padding:5px;
		padding-bottom:2px;
		border-bottom:0;
		color: #7C7979;
		font-size:16px;
		margin-bottom:0;
		top:-5px
	}
</style>
<body>
	<div id="window"></div>
	<div class="container-fluid">
		<span id="notification" style="display:none;"></span>
		<?php if(!empty($user)): ?>
		<div id="header" class="row">
			
			<div class="col-lg-12">
				<ul id="menu-principal" class="menu-p">
					<li><a href="#">Hola <b><?=$user->username?></b></a></li>
					<li>
						<center>
							<a href="<?=$url("cms")?>">
								<img class="icn_responsive-m" src="<?=$urlm("images/icons/start_48px.png")?>">
							</a>
						</center>
					</li>
					<li>
						<a href="">
							<img class="icn_responsive-m" src="<?=$urlm("images/icons/Contentmen_48px.png")?>">
							Contenido
						</a>
						<ul >
							<li >
								<div class="template">
									<center>
										<img class="icn_responsive-t" class="" src="<?=$urlm("images/icons/Contentmen_48px.png")?>">
										<h3>Contenido</h3>
									</center>
									<table width="100%">
										<tr>
											<td><a href="<?=$url("cms/content")?>">Artículos</a></td>
											<td><img class="icn_responsive-m" src="<?=$urlm("images/icons/articles_48px.png")?>"></td>
										</tr>
										<tr>
											<td><a href="<?=$url("cms/block")?>">Bloques</a></td>
											<td><img class="icn_responsive-m" src="<?=$urlm("images/icons/menus_48px.png")?>"></td>
										</tr>
										<tr>
											<td><a href="<?=$url("cms/listdata")?>">List Data</a></td>
											<td><img class="icn_responsive-m" src="<?=$urlm("images/icons/block_48px.png")?>"></td>
										</tr>
									</table>
								</div>
							</li>
						</ul>
					</li>
					<li>
						<img class="icn_responsive-m" src="<?=$urlm("images/icons/Layout_48px.png")?>">
							Estructura
						<ul >
							<li >
								<div class="template">
									<center>
										<img class="icn_responsive-t" class="" src="<?=$urlm("images/icons/Layout_48px.png")?>">
										<h3>Estructura</h3>
									</center>
									<table width="100%">
										<tr>
											<td><a href="<?=$url("cms/region")?>">Regiones</a></td>
											<td><img class="icn_responsive-m" class="" src="<?=$urlm("images/icons/block_48px.png")?>"></td>
										</tr>
										<tr>
											<td><a href="<?=$url("cms/template")?>">Plantillas</a></td>
											<td><img class="icn_responsive-m" class="" src="<?=$urlm("images/icons/views_48px.png")?>"></td>
										</tr>
										<tr>
											<td><a href="<?=$url("cms/contenttype")?>">Tipos de contenido</a></td>
											<td><img class="icn_responsive-m" class="" src="http://<?=$burl.$murl?>/images/icons/Document_48px.png"></td>
										</tr>
									</table>
								</div>
							</li>
						</ul>
					</li>
					<li>
						<a href="javascript:multimedia()">
							<img class="icn_responsive-m" class="" src="http://<?=$burl.$murl?>/images/icons/Multimedia_48px.png">
							Multimedia
						</a>
					</li>
					<li>
						<img class="icn_responsive-m" src="<?=$urlm("images/icons/Settings_48px.png")?>"/>
						<ul>
							<li>
								<img class="icn_responsive-m" src="<?=$urlm("images/icons/User_48px.png")?>"> Credenciales
								<ul >
									<li >
										<div class="template">
											<center>
												<img class="icn_responsive-t" class="" src="<?=$urlm("images/icons/User_48px.png")?>">
												<h3>Credenciales</h3>
												
											</center>
											<table width="100%">
												<tr>
													<td><a href="<?=$url("cms/user")?>">Usuarios</a></td>
													<td><img class="icn_responsive-m" src="<?=$urlm("images/icons/Users_48px.png")?>"></td>
												</tr>
												<tr>
													<td><a href="<?=$url("cms/rol")?>">Roles</a></td>
													<td><img class="icn_responsive-m" src="<?=$urlm("images/icons/profile_48px.png")?>"></td>
												</tr>
												<tr>
													<td><a href="<?=$url("cms/permiso")?>">Permisos</a></td>
													<td><img class="icn_responsive-m" class="" src="<?=$urlm("images/icons/permiso_48px.png")?>"></td>
												</tr>
											</table>
										</div>
									</li>
								</ul>
							</li>
							<li>
								<img class="icn_responsive-m" class="" src="<?=$urlm("images/icons/log_48px.png")?>"> Log
							</li>
						</ul>
						
						<li><a style="color:blue" href="<?=$url("cms/auth/logout")?>">Cerrar Sesión</a></li>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<div id="content-cms" style="min-height:500px">
		<?=$content?>
	</div>
	<div class="container-fluid">	
		<div id="footer" style="height:100px;background:#D8D8D8;padding-top:20px;font-size:10px" >
			<center>
				<b>2018. Cuarto 101 SA de CV<br/>
				   Todos los derechos reservados
				</b>
			</center>
		</div>
	</div>
</div>
<style>
		.demo-section p {
			margin: 3px 0 20px;
			line-height: 50px;
		}
		.demo-section .k-button {
			width: 250px;
		}

		.k-notification {
			border: 0;
		}
		/* Info template */
		.k-notification-info.k-group {
			background: rgba(0%,0%,0%,.7);
			color: #fff;
		}
		.new-mail {
			width: 300px;
			height: 100px;
		}
		.new-mail h3 {
			font-size: 1em;
			padding: 32px 10px 5px;
		}
		.new-mail img {
			float: left;
			margin: 30px 15px 30px 30px;
		}

		/* Error template */
		.k-notification-error.k-group {
			background: rgba(100%,0%,0%,.7);
			color: #ffffff;
		}
		.wrong-pass {
			width: 300px;
			padding:20px
		}
		.wrong-pass h3 {
			font-size: 1em;
			padding: 32px 10px 5px;
		}
		.wrong-pass img {
			float: left;
			margin-right:20px
		}

		/* Success template */
		.k-notification-upload-success.k-group {
			background: rgba(0%,60%,0%,.7);
			color: #fff;
		}
		.upload-success {
			width: 240px;
			padding: 20px;
			line-height: 50px;
		}
		.upload-success h3 {
			font-size: 14px;
			font-weight: normal;
			display: inline-block;
			vertical-align: middle;
		}
		.upload-success img {
			display: inline-block;
			vertical-align: middle;
			margin-right: 10px;
		}
	</style>
	<script id="errorTemplate" type="text/x-kendo-template">
		<div class="wrong-pass" >
			<img src="<?=$url("images/error-icon.png")?>" />
			<span>#= title #</span>
			<p>#= message #</p>
		</div>
	</script>
	<script id="successTemplate" type="text/x-kendo-template">
		<div class="upload-success">
			<img src="<?=$url("images/success-icon.png")?>" />
			<h3>#= message #</h3>
		</div>
	</script>
	<script>
		var notification = null;
		var myWindow = null;
		$(document).ready(function (){
			$("#menu-principal").kendoMenu();
			$("#menu-conf").kendoMenu();
			notification = $("#notification").kendoNotification({
				position: {
					pinned: true,
					top: 30,
					right: 30
				},
				autoHideAfter: 8000,
				stacking: "down",
				templates: [
					{
						type: "error",
						template: $("#errorTemplate").html()
					}, {
						type: "upload-success",
						template: $("#successTemplate").html()
					}
				]
			}).data("kendoNotification");

			myWindow = $("#window"),

            myWindow.kendoWindow({
                width: "800px",
                height: "550px",
                title: "Multimedia",
                visible: false,
                content: "<?=$url("cms/multimedia")?>",
                position: {
					top: 50, // or "100px"
					left: "30%"
				},
                actions: [
                    "Pin",
                    "Minimize",
                    "Maximize",
                    "Close"
                ]
            });
			
		});

		function multimedia(){
			myWindow.data("kendoWindow").open();
		}
		
		
	</script>
</body>