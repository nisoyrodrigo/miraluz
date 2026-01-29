<!DOCTYPE html>
<html lang="es-MX">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Óptica Miraluz">


    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" href="<?=$urlm('assets/favicon/apple-touch-icon.png');?>">

    <!-- Android Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="<?=$urlm('assets/favicon/android-chrome-192x192.png');?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?=$urlm('assets/favicon/android-chrome-512x512.png');?>">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?=$urlm('assets/favicon/favicon-32x32.png');?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=$urlm('assets/favicon/favicon-16x16.png');?>">
    <link rel="icon" type="image/x-icon" href="<?=$urlm('assets/favicon/favicon.ico');?>">

    <!-- Manifest -->
    <link rel="manifest" href="<?=$urlm('assets/favicon/site.webmanifest');?>">

    <!-- Opcionales (si quieres mantenerlos) -->
    <meta name="msapplication-TileColor" content="#000000">



    <title>Óptica Miraluz®</title>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
    <script type="text/javascript" src="https://unpkg.com/vuex@3.1.2/dist/vuex.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.js"></script>

    <!-- Styles -->
    <link href="<?=$urlm("assets/css/bootstrap.min.css");?>" rel="stylesheet" type="text/css" id="bootstrap">
    <link href="<?=$urlm("assets/css/plugins.css");?>" rel="stylesheet" type="text/css" >
    <link href="<?=$urlm("assets/css/style.css");?>" rel="stylesheet" type="text/css" >
    <link href="<?=$urlm("assets/css/coloring.css");?>" rel="stylesheet" type="text/css" >
    <!-- color scheme -->
    <link id="colors" href="<?=$urlm("assets/css/colors/scheme-01.css");?>" rel="stylesheet" type="text/css" >
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>


  </head>
  <body>
    <div id="wrapper">
      <a href="#" id="back-to-top"></a>
      <!-- page preloader begin -->
      <div id="de-loader"></div>
      <!-- page preloader close -->
      <?php include_once(Motor::app()->absolute_url.$murl."/templates/header.tpl.php"); ?>
      <main>
        <div id="top"></div>
        <?=$region("Body")?>
        <?php print $content; ?>
      </main>
      <?php include_once(Motor::app()->absolute_url.$murl."/templates/footer.tpl.php"); ?>
    </div>


    <!-- Core JavaScript -->
    <script src="<?=$urlm("assets/js/plugins.js");?>"></script>
    <script src="<?=$urlm("assets/js/designesia.js");?>"></script>
    <script src="<?=$urlm("assets/js/validation-contact.js");?>"></script>

  </body>
</html>