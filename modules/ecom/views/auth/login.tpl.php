<!DOCTYPE html>
<html lang="es">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin</title>

    <!-- Bootstrap -->
    <link href="<?=$urlm("css/bootstrap.min.css")?>" rel="stylesheet">
     <!-- Animate.css -->
    <link href="<?=$urlm("css/animate.css")?>" rel="stylesheet">
    <link href="<?=$urlm("css/style.css")?>" rel="stylesheet">

    <!-- Custom Style -->
    <link href="<?=$urlm("css/custom-style.css")?>" rel="stylesheet">

  </head>

  <body class="login gray-bg">

    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>

    <div class="middle-box text-center loginscreen animated fadeInDown margin-top-4">
      <div>
        <div>
          <h1 class="logo-name"><img class="img-responsive" src="<?=$url("images/logo-optica.png");?>" width="200"/></h1>
        </div>
        <h3 class="margin-top-4">Bienvenido</h3>
        <p>¡Hola!</p>
        <p>Inicia sesión para ingresar</p>
        <form class="m-t" role="form" action="https://<?=$burl?>ecom/auth/login" method="POST">
            <div class="form-group">
                <input type="text" id="username" name="username" class="form-control" placeholder="Usuario" required="" value="">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required="" value="">
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">Entrar</button>
            <span class="error"><?=$error?></span>
        </form>
        <p class="m-t"> <small>Todos los derechos reservados &copy; Cuarto 101 2024</small> </p>
      </div>
    </div>
  </body>
</html>