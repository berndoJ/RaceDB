<?php

// Check for authorisation. Minimum required permissionlevel: manager.

session_start();

if (!isset($_SESSION["permissionlevel"])) {
    header("Location: ../login.php");
    exit();
}

$permission_level = $_SESSION["permissionlevel"];

require_once __DIR__ . "/../includes/permissionlevel.inc.php";

if ($permission_level < $_PERMISSION_LEVELS["manager"]) {
    header("Location: ../index.php");
    exit();
}

// Check for presence of important attributes.

if (!isset($_POST["name"])) {
    echo "Invalid attributes.";
    exit();
}

$name = trim($_POST["name"]);

if (empty($name)) {
    echo "Empty attributes.";
    exit();
}

// Open DB connection.

require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    header("Location: ../edit_runs.php?error=bad_sql");
    exit();
}

// Check if the name already exists.

$sql_query = "SELECT * FROM runs WHERE name=?;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    header("Location: ../edit_runs.php?error=bad_sql");
    exit();
}
mysqli_stmt_bind_param($sql_stmt, "s", $name);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);
if (mysqli_num_rows($sql_result) > 0) {
    header("Location: ../edit_runs.php?error=name_taken");
    exit();
}
mysqli_free_result($sql_result);

// Create the new run (SQL-query).

$sql_query = "INSERT INTO runs (name, active) VALUES (?, ?);";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    header("Location: ../edit_runs.php?error=bad_sql");
    exit();
}
mysqli_stmt_bind_param($sql_stmt, "si", $name, 0);
mysqli_stmt_execute($sql_stmt);

// Close the database connection and redirect back to the origin site and display the success message.

mysqli_close($db_conn);

header("Location: ../edit_runs.php?success=create_run");
exit();
