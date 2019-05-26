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
  header("Location: ../manage_users.php?error=delete_failed");
  exit();
}

$userid = $_POST["userid"];

if (!isset($_SESSION["userid"]))
{
  header("Location: ../manage_users.php?error=delete_failed");
  exit();
}

if ($_SESSION["userid"] == $userid)
{
  header("Location: ../manage_users.php?error=delete_failed");
  exit();
}

require_once __DIR__."/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn)
{
  header("Location: ../manage_users.php?error=delete_failed");
  exit();
}

$sql_query = "DELETE FROM users WHERE id=?;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
{
  header("Location: ../manage_users.php?error=delete_failed");
  exit();
}
mysqli_stmt_bind_param($sql_stmt, "i", $userid);
mysqli_stmt_execute($sql_stmt);
mysqli_close($db_conn);

header("Location: ../manage_users.php?success=delete");
exit();

?>
