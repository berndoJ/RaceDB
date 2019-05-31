<?php

// Send table header.
echo "<tr><th>Staffelname</th><th>Aktionen</th></tr>";

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<tr><td colspan=\"2\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"2\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Run SQL query.
$sql_query = "SELECT * FROM relays WHERE runid=? ORDER BY name ASC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"2\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"2\" style=\"text-align: center;\"><i>Es wurden keine Staffeln erstellt.</i></td></tr>";
    exit;
}

// Construct table html.
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $relay_name = $sql_row["name"];

    $table_html = "<tr><td>"
        . $relay_name
        . "</td><td><ul><li><a onclick=\"delete_relay_click("
        . $sql_row["id"]
        . ");\" href=\"#\">Löschen</a></li><li><a onclick=\"edit_relay_click("
        . $sql_row["id"]
        . ");\" href=\"#\">Bearbeiten</a></li></ul></td></tr>";

    echo $table_html;
}

// Close database connection.
mysqli_close($db_conn);