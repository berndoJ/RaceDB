<?php
session_start();

require_once __DIR__."/../config/cfghandler.inc.php";
$config = retrieve_default_config();

if ($config->get("setup.complete") != TRUE)
{
  header("Location: setup.php");
  exit();
}
?>
