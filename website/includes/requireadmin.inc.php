<?php

if (!isset($_SESSION["permissionlevel"]))
{
  header("Location: ./login_required.php");
  exit();
}

$permission_level = $_SESSION["permissionlevel"];

require_once __DIR__."/permissionlevel.inc.php";

if ($permission_level !== $_PERMISSION_LEVELS["admin"])
{
  header("Location: ./index.php");
  exit();
}

?>
