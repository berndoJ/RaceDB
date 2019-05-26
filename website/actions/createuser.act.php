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

if (isset($_POST["createuser-submit"]))
{

  require_once __DIR__."/../includes/db.inc.php";
  $db_conn = open_db_connection();
  if (!$db_conn)
  {
    header("Location: ../create_user.php?error=bad_sql");
    exit();
  }

  $username = $_POST["createuser-username"];
  $password = $_POST["createuser-password"];
  $password_verify = $_POST["createuser-password-confirm"];
  $permission_level_str = $_POST["createuser-permissionlevel"];

  $username = isset($username) ? trim($username) : false;
  $password = isset($password) ? trim($password) : false;
  $password_verify = isset($password_verify) ? trim($password_verify) : false;
  $permission_level_str = isset($permission_level_str) ? trim($permission_level_str) : false;

  if (empty($username) || empty($password) || empty($password_verify) || empty($permission_level_str))
  {
    header("Location: ../create_user.php?error=empty_fields");
    exit();
  }

  // Check if the passwords entered match
  if ($password !== $password_verify)
  {
    header("Location: ../create_user.php?error=password_match_failed");
    exit();
  }

  // Check if the username is already taken
  $sql_query = "SELECT * FROM users WHERE username=?";
  $sql_stmt = mysqli_stmt_init($db_conn);
  if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
  {
    header("Location: ../create_user.php?error=bad_sql");
    exit();
  }
  mysqli_stmt_bind_param($sql_stmt, "s", $username);
  mysqli_stmt_execute($sql_stmt);
  $sql_result = mysqli_stmt_get_result($sql_stmt);
  if (mysqli_num_rows($sql_result) != 0)
  {
    header("Location: ../create_user.php?error=username_taken");
    exit();
  }

  // Parse the permission level input
  require_once __DIR__."/../includes/permissionlevel.inc.php";
  $permission_level = str_to_permissionlevel($permission_level_str);
  if ($permission_level < 0)
  {
    header("Location: ../create_user.php?error=invalid_permission_level");
    exit();
  }

  // Gernerate the password hash.
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // Insert the new user into the database.
  $sql_query = "INSERT INTO users (username, passwd, permissionlevel) VALUES (?, ?, ?)";
  $sql_stmt = mysqli_stmt_init($db_conn);
  if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
  {
    header("Location: ../create_users.php?error=bad_sql");
    exit();
  }
  mysqli_stmt_bind_param($sql_stmt, "ssi", $username, $password_hash, $permission_level);
  mysqli_stmt_execute($sql_stmt);

  mysqli_stmt_close($sql_stmt);
  mysqli_close($db_conn);

  header("Location: ../settings.php?info=create_user_success&user=".$username);
  exit();

}
else
{
  header("Location: ../create_user.php");
  exit();
}

?>
