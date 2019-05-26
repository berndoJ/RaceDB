<?php

session_start();

if (!isset($_SESSION["username"]))
{
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
$sql_query = "SELECT username FROM users WHERE id=?;";
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
  $username = $sql_row["username"];
  mysqli_free_result($sql_result);
  mysqli_close($db_conn);
  echo $username;
  exit;
}
mysqli_free_result($sql_result);
mysqli_close($db_conn);
echo "n.a.";
exit;

?>
