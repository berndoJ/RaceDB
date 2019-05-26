<?php

function open_db_connection()
{
  require_once __DIR__."/../config/cfghandler.inc.php";
  $config = retrieve_default_config();

  $db_conn = mysqli_connect($config->get("db.hostname"), $config->get("db.username"), $config->get("db.password"), $config->get("db.dbname"));

  if (!$db_conn)
  {
    return null;
  }

  return $db_conn;
}

/*
require "../version/setupinfo.php";

$db_servername = $SETUP_SQL_HOSTNAME;
$db_username = $SETUP_SQL_USERNAME;
$db_password = $SETUP_SQL_PASSWORD;
$db_database_name = $SETUP_SQL_DATABASENAME;

if (!$SETUP_COMPLETE)
{
  exit("SQL-Database not set up. Please go to setup.php");
}

$db_conn = mysqli_connect($db_servername, $db_username, $db_password, $db_database_name);

if (!$db_conn)
{
  exit("SQL-Database connection failed: ".mysqli_connect_error());
}

function db_update()
{
  require "../version/setupinfo.php";

  $db_servername = $SETUP_SQL_HOSTNAME;
  $db_username = $SETUP_SQL_USERNAME;
  $db_password = $SETUP_SQL_PASSWORD;
  $db_database_name = $SETUP_SQL_DATABASENAME;

  if (!$SETUP_COMPLETE)
  {
    exit("SQL-Database not set up. Please go to setup.php");
  }

  $db_conn = mysqli_connect($db_servername, $db_username, $db_password, $db_database_name);

  if (!$db_conn)
  {
    exit("SQL-Database connection failed: ".mysqli_connect_error());
  }
}
*/

?>
