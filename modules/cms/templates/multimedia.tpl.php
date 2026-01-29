<html>
	<head>
		<title>Administrador</title>
	</head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
<body>
	<span id="notification" style="display:none;"></span>
	<div id="content" style="height:400px">
		<?=$content?>
	</div>
</div>
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
		.k-grid-content{
			height:300px!important
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

		});
	</script>
</body>