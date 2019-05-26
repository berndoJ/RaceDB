<?php

if (isset($_POST["login-submit"]))
{
  require_once __DIR__."/../includes/db.inc.php";
  $db_conn = open_db_connection();
  if (!$db_conn)
  {
    header("Location: ../login.php?error=bad_sql");
    exit();
  }

  $username = $_POST["login-username"];
  $password = $_POST["login-password"];

  if (empty($username) || empty($password))
  {
    header("Location: ../login.php?error=empty_fields");
    exit();
  }

  $sql_query = "SELECT * FROM users WHERE username=?;";
  $sql_stmt = mysqli_stmt_init($db_conn);
  if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
  {
    header("Location: ../login.php?error=bad_sql");
    exit();
  }
  mysqli_stmt_bind_param($sql_stmt, "s", $username);
  mysqli_stmt_execute($sql_stmt);
  $sql_result = mysqli_stmt_get_result($sql_stmt);
  if ($sql_row = mysqli_fetch_assoc($sql_result))
  {
    $password_check = password_verify($password, $sql_row["passwd"]);
    if ($password_check == false)
    {
      header("Location: ../login.php?error=invalid_password");
      exit();
    }
    else if ($password_check == true)
    {
      session_start();

      $_SESSION["userid"] = $sql_row["id"];
      $_SESSION["username"] = $sql_row["username"];
      $_SESSION["permissionlevel"] = $sql_row["permissionlevel"];

      mysqli_stmt_close($sql_stmt);
      mysqli_close($db_conn);

      header("Location: ../index.php?login=success");
      exit();
    }
    else
    {
      header("Location: ../login.php?error=invalid_password");
      exit();
    }
  }
  else
  {
    header("Location: ../login.php?error=invalid_username");
    exit();
  }

}
else
{
  header("Location: ../login.php");
  exit();
}

?>
