<?php

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<option value=\"\"><i>Variable \"runid\" fehlt.</i></option>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<option value=\"\"><i>SQL-Fehler</i></option>";
    exit;
}

// Run SQL query.
$sql_query = "SELECT * FROM relays WHERE runid=? ORDER BY name ASC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<option value=\"\"><i>SQL-Fehler</i></option>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<option value=\"\"><i>Keine Staffeln vorhanden</i></option>";
    exit;
}

// Construct select html.
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $relay_id = $sql_row["id"];
    $relay_name = $sql_row["name"];

    $select_html = "<option value=\""
        . $relay_id
        . "\">"
        . $relay_name
        . "</option>";

    echo $select_html;
}

// Close database connection.
mysqli_close($db_conn);
