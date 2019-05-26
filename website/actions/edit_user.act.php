<?php
session_start();

if (!isset($_SESSION["permissionlevel"]))
{
  header("Location: ../login.php");
  exit();
}

$permission_level = $_SESSION["permissionlevel"];

require_once __DIR__."/../includes/permissionlevel.inc.php";

if ($permission_level !== $_PERMISSION_LEVELS["admin"])
{
  header("Location: ../index.php");
  exit();
}


if (!isset($_POST["userid"]))
{
  header("Location: ../manage_users.php?error=edit_password_failed");
  exit();
}

$userid = $_POST["userid"];

if (!isset($_SESSION["userid"]))
{
  header("Location: ../manage_users.php?error=edit_failed");
  exit();
}

if ($_SESSION["userid"] == $userid)
{
  header("Location: ../manage_users.php?error=edit_failed");
  exit();
}

if (isset($_POST["password"]))
{
  $password = $_POST["password"];

  require_once __DIR__."/../includes/db.inc.php";
  $db_conn = open_db_connection();
  if (!$db_conn)
  {
    header("Location: ../manage_users.php?error=edit_failed");
    exit();
  }

  $sql_query = "UPDATE users SET passwd=? WHERE id=?;";
  $sql_stmt = mysqli_stmt_init($db_conn);
  if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
  {
    header("Location: ../manage_users.php?error=edit_failed");
    exit();
  }
  mysqli_stmt_bind_param($sql_stmt, "si", password_hash($password, PASSWORD_DEFAULT), $userid);
  mysqli_stmt_execute($sql_stmt);
  mysqli_close($db_conn);
}

if (isset($_POST["permissionlevel"]))
{
  require_once __DIR__."/../includes/permissionlevel.inc.php";
  $permission_level_str = $_POST["permissionlevel"];
  $permission_level = str_to_permissionlevel($permission_level_str);

  require_once __DIR__."/../includes/db.inc.php";
  $db_conn = open_db_connection();
  if (!$db_conn)
  {
    header("Location: ../manage_users.php?error=edit_failed");
    exit();
  }

  $sql_query = "UPDATE users SET permissionlevel=? WHERE id=?;";
  $sql_stmt = mysqli_stmt_init($db_conn);
  if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
  {
    header("Location: ../manage_users.php?error=edit_failed");
    exit();
  }
  mysqli_stmt_bind_param($sql_stmt, "ii", $permission_level, $userid);
  mysqli_stmt_execute($sql_stmt);
  mysqli_close($db_conn);
}

header("Location: ../manage_users.php?success=edit");
exit();

?>
