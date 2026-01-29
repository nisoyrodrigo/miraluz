<div class="row">
	<div class="col-lg-12">
		<center>
			<br/><br/>
			<img src="<?=$url("modules/web/images/Logo.jpg")?>">
			<h1>Administrador de Contenidos</h1>
			<br/><br/>
			<form method="post" action="<?=$url("cms/auth/login")?>" style="background:#F5F6CE; border-radius:30px">
				
				<table style="min-height:250px">
					<tr>
						<td style="width:100px">Usuario</td>
						<td><input type="text" class="k-input k-textbox" name="username" value="<?=$username?>"></td>
					</tr>
					<tr>
						<td>Password</td>
						<td><input type="password" class="k-input k-textbox" name="password" value="<?=$username?>"></td>
					</tr>
					<tr>
						<td colspan="2">
							<center><input type="submit" class="k-button" value="Validar"></center>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<span class="error"><?=$error?></span>
						</td>
					</tr>
				</table>
			</form>
		<center>
	</div>
</div>
