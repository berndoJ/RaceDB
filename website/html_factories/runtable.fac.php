<?php

echo "<tr><th>Laufname</th><th>Status</th><th>Aktionen</th></tr>";

require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL error.</td></tr>";
    exit;
}

$sql_query = "SELECT * FROM runs;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL error.</td></tr>";
    exit;
}
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\"><i>Es wurden keine Läufe erstellt.</i></td></tr>";
    exit;
}

while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $run_active = $sql_row["active"] ? "<b>Aktiv</b>" : "Inaktiv";

    $table_html = "<tr><td>"
        . $sql_row["name"]
        . "</td><td>"
        . $run_active
        . "</td><td><ul><li><a onclick=\"delete_run_click("
        . $sql_row["id"]
        . ");\" href=\"#\">Löschen</a></li><li><a onclick=\"edit_run_click("
        . $sql_row["id"]
        . ");\" href=\"#\">Bearbeiten</a></li>";

    if ($sql_row["active"] != true) {
        $table_html .=
            "<li><a onclick=\"activate_run_click("
            . $sql_row["id"]
            . ");\" href=\"#\">Aktivieren</a></li>";
    }

    $table_html .= "</ul></td></tr>";

    echo $table_html;
}
