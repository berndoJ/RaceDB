<?php

session_start();

if (!isset($_SESSION["username"]))
{
  echo "n.a.";
  exit;
}

if (!isset($_GET["userid"]))
{
  echo "n.a.";
  exit;
}

require_once __DIR__."/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn)
{
  echo "n.a.";
  exit;
}
$sql_query = "SELECT permissionlevel FROM users WHERE id=?;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
{
  echo "n.a.";
  exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["userid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);
if ($sql_row = mysqli_fetch_assoc($sql_result))
{
  $permission_level = $sql_row["permissionlevel"];
  mysqli_free_result($sql_result);
  mysqli_close($db_conn);

  if (isset($_GET["text"]))
  {
    require_once __DIR__."/../includes/permissionlevel.inc.php";
    $permission_level_str = permissionlevel_to_str($permission_level);
    echo $permission_level_str;
    exit;
  }

  echo $permission_level;
  exit;
}
mysqli_free_result($sql_result);
mysqli_close($db_conn);
echo "n.a.";
exit;

?>
