<?php
$no_leidas = 0;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="<?=$url("images/favicon/apple-icon-57x57.png");?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?=$url("images/favicon/apple-icon-60x60.png");?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=$url("images/favicon/apple-icon-72x72.png");?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=$url("images/favicon/apple-icon-76x76.png");?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=$url("images/favicon/apple-icon-114x114.png");?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=$url("images/favicon/apple-icon-120x120.png");?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?=$url("images/favicon/apple-icon-144x144.png");?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=$url("images/favicon/apple-icon-152x152.png");?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=$url("images/favicon/apple-icon-180x180.png");?>">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?=$url("images/favicon/android-icon-192x192.png");?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=$url("images/favicon/favicon-32x32.png");?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?=$url("images/favicon/favicon-96x96.png");?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=$url("images/favicon/favicon-16x16.png");?>">
    <link rel="manifest" href="<?=$url("images/favicon/manifest.json");?>">
    <meta name="msapplication-TileColor" content="#000000">
    <meta name="msapplication-TileImage" content="<?=$url("images/favicon/ms-icon-144x144.png");?>">
    <meta name="theme-color" content="#000000">
    <title>Administrador</title>


    <?php include_once(Motor::app()->absolute_url.$murl."/templates/css.tpl.php"); ?>
    <script src="<?=$urlm("js/jquery-2.1.1.js")?>"></script>
  </head>

  <body>

    <div id="wrapper">
      <?php include_once(Motor::app()->absolute_url.$murl."/templates/menu.tpl.php"); ?>
        
      <div id="page-wrapper" class="gray-bg dashbard-1">
        <?php include_once(Motor::app()->absolute_url.$murl."/templates/topbar.tpl.php"); ?>
        <div class="ecom-content">
          <?=$content;?>
        </div>
        <div class="footer">
          <div class="pull-right">
            <strong>contacto@cuarto101.mx</strong>
          </div>
          <div>
            <strong>Copyright</strong> Cuarto 101 &copy; 2024
          </div>
        </div>
          
      </div>
    </div>

    <?php include_once(Motor::app()->absolute_url.$murl."/templates/js.tpl.php"); ?>

  </body>
</html>