<?php

  require_once("config.php");
  require_once("../packages.php");

  header("Content-Type: image/png");
  if (isset($_GET["package"]))
  {
    $name = $_GET["package"];
    $packages = getPackages();
    if (array_key_exists($name, $packages))
    {
      $package = $packages[$name];
      if (array_key_exists("Maemo_Icon_40", $package))
      {
        print(base64_decode($package["Maemo_Icon_40"]));
        exit;
      }
      if (array_key_exists("Maemo_Icon_26", $package))
      {
        print(base64_decode($package["Maemo_Icon_26"]));
        exit;
      }
    }
  }
  print(file_get_contents("package.png"));

?>