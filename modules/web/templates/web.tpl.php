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
    <meta name="msapplication-TileColor" content="#1a1f1a">
    <meta name="theme-color" content="#1a1f1a">

    <title>Óptica Miraluz®</title>

    <!-- Styles -->
    <link href="<?=$urlm("assets/css/bootstrap.min.css");?>" rel="stylesheet" type="text/css">
    <link href="<?=$urlm("assets/css/home-miraluz.css");?>" rel="stylesheet" type="text/css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Vue (si lo necesitas para otras páginas) -->
    <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
    <script src="https://unpkg.com/vuex@3.1.2/dist/vuex.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.js"></script>
  </head>

  <body class="ml-home">
    <!-- Cursor Glow (desktop only) -->
    <div class="ml-cursor-glow" id="mlCursorGlow"></div>

    <!-- Loader -->
    <div class="ml-loader" id="mlLoader">
      <div class="ml-loader-eye">
        <div class="ring"></div>
        <div class="ring"></div>
        <div class="ring"></div>
        <div class="pupil"></div>
      </div>
      <span class="ml-loader-text">CARGANDO...</span>
    </div>

    <!-- Header -->
    <?php include_once(Motor::app()->absolute_url.$murl."/templates/header.tpl.php"); ?>

    <!-- Main Content -->
    <main>
      <?=$region("Body")?>
      <?php print $content; ?>
    </main>

    <!-- Footer -->
    <?php include_once(Motor::app()->absolute_url.$murl."/templates/footer.tpl.php"); ?>

    <!-- JavaScript -->
    <script src="<?=$urlm("assets/js/home-miraluz.js");?>"></script>
  </body>
</html>
