<?php

/**
 * HTML factory for producing checkboxes of all not already started relays.
 */

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<i>Variable \"runid\" fehlt.</i>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<i><b>SQL-Fehler</b></i>";
    exit;
}

// Run SQL query.
$sql_query = "SELECT * FROM relays WHERE runid=? AND id NOT IN (SELECT relayid FROM startevents) ORDER BY name ASC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<i><b>SQL-Fehler</b></i>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<i>Keine Staffeln Vorhanden.</i>";
    exit;
}

// Construct select html.
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $relay_id = $sql_row["id"];
    $relay_name = $sql_row["name"];

    $checkbox_html = "<label class=\"ccb_container\">"
        . $relay_name
        . "<input type=\"checkbox\" class=\"__RELAY_CHECKBOX\" id=\""
        . $relay_id
        . "\"/><span class=\"ccb_checkmark\"></span></label>";

    echo $checkbox_html;
}

// Close database connection.
mysqli_close($db_conn);
