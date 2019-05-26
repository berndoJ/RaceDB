<?php

if (isset($_POST["setup-submit"]))
{
  require_once __DIR__."/../config/cfghandler.inc.php";
  $config = retrieve_default_config();

  if ($config->get("setup.complete") != TRUE)
  {
    // Retrieve the entered credentials from the POST request.
    $setup_form_sql_hostname = $_POST["setup-sqlhostname"];
    $setup_form_sql_username = $_POST["setup-sqlusername"];
    $setup_form_sql_password = $_POST["setup-sqlpassword"];
    $setup_form_sql_database_name = $_POST["setup-sqldbname"];

    // Test the SQL connection credentials and test the connection to validate them.
    $sql_test_conn = mysqli_connect($setup_form_sql_hostname, $setup_form_sql_username, $setup_form_sql_password, $setup_form_sql_database_name);

    if (!$sql_test_conn)
    {
      header("Location: ../setup.php?error=bad_sql_credentials");
      exit();
    }

    mysqli_close($sql_test_conn);

    $config->set("db.hostname", $setup_form_sql_hostname);
    $config->set("db.username", $setup_form_sql_username);
    $config->set("db.password", $setup_form_sql_password);
    $config->set("db.dbname", $setup_form_sql_database_name);

    $config->save();

    // Produce a new, clean SQL database.
    require_once __DIR__."/../includes/database_factory.inc.php";
    db_factory_produce();

    // Add a default admin user.
    require_once __DIR__."/../includes/db.inc.php";
    $db_conn = open_db_connection();
    if (!$db_conn)
    {
      exit("Bad SQL connection while creating default admin user. Contact developer.");
    }
    else
    {
      $sql_query = "INSERT INTO users (username, passwd, permissionlevel) VALUES (\"admin\", \"".password_hash("admin", PASSWORD_DEFAULT)."\", 10000)";
      $sql_stmt = mysqli_stmt_init($db_conn);
      if (!mysqli_stmt_prepare($sql_stmt, $sql_query))
      {
        exit("Bad SQL while creating default admin user. Contact developer.");
      }
      mysqli_stmt_execute($sql_stmt);
    }

    $config->reload();

    $config->set("setup.complete", true);

    $config->save();

    header("Location: ../login.php");
    exit("Success");

  }
  else
  {
    header("Location: ../login.php");
    exit();
  }
}
else
{
  exit("Incorrect call.");
}

?>
