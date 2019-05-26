<?php

if (!isset($_SESSION["permissionlevel"]))
{
  header("Location: ./login_required.php");
  exit();
}

require_once __DIR__."/permissionlevel.inc.php";

$permission_level = $_SESSION["permissionlevel"];

if ($permission_level < $_PERMISSION_LEVEL_DEFAULT)
{
  header("Location: ./index.php");
  exit();
}

?>
