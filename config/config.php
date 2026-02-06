<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require_once dirname(__DIR__) . "/base/Env.php";
Env::load();

require_once "lib/FPDF/fpdf.php";

date_default_timezone_set("America/Mexico_City");

  $config = array(
    "aws_key" => Env::get("AWS_KEY"),
    "aws_secret" => Env::get("AWS_SECRET"),
    "checkUrlCMS" => array(
      "active" => true,
      "module" => "web",
      "controller" => "View",
      "model" => "CheckURL",
      "field" => "url"
    ),
    "modules" => array(
      "web" => array(
        "active" => true,
        "default" => true,
        "template" => "web",
        "https" => true
      ),
      "cms" => array(
        "active" => true,
        "template" => "cms",
        "registerSections" => false,
        "prefix_tables" => "cms",
        "https" => true,
        "auth" => array(
          "controllerAuth" => "AuthController",
          "loginAction" => "login",
          "logoutAction" => "logout"
        )
      ),
      "ecom" => array(
        "active" => true,
        "default" => false,
        "template" => "ec",
        "registerSections" => true,
        "prefix_tables" => "ec",
        "https" => true,
        "auth" => array(
          "controllerAuth" => "AuthController",
          "loginAction" => "login",
          "logoutAction" => "logout"
        )
      ),
    ),
    "DB" => array(
      "host"     => Env::get("DB_HOST", "localhost"),
      "user"     => Env::get("DB_USER"),
      "password" => Env::get("DB_PASSWORD"),
      "db"       => Env::get("DB_NAME")
    )
  );
?>
