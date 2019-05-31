<?php

require_once __DIR__ . "/../includes/msg.inc.php";

// Check for authorisation. Minimum required permissionlevel: manager.
session_start();
if (!isset($_SESSION["permissionlevel"])) {
    exit(MSG_ERROR_NOT_LOGGED_IN);
}
$permission_level = $_SESSION["permissionlevel"];
require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($permission_level < $_PERMISSION_LEVELS["manager"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

// Check for presence of important attributes.

if (!isset($_POST["name"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$name = trim($_POST["name"]);

if (empty($name)) {
    exit(MSG_ERROR_VARIABLES_INVALID);
}

// Open DB connection.

require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    exit(MSG_ERROR_SQL_NO_CONNECTION);
}

// Check if the name already exists.

$sql_query = "SELECT * FROM runs WHERE name=?;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    exit(MSG_ERROR_SQL_BAD_QUERY);
}
mysqli_stmt_bind_param($sql_stmt, "s", $name);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);
if (mysqli_num_rows($sql_result) > 0) {
    exit("ERR_NAME_TAKEN\n$name");
}
mysqli_free_result($sql_result);

// Create the new run (SQL-query).

$sql_query = "INSERT INTO runs (name, active) VALUES (?, ?);";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    exit(MSG_ERROR_SQL_BAD_QUERY);
}
$def_run_active = 0;
mysqli_stmt_bind_param($sql_stmt, "si", $name, $def_run_active);
mysqli_stmt_execute($sql_stmt);

// Close the database connection and return.

mysqli_close($db_conn);

exit(MSG_SUCCESS . "\n$name");
