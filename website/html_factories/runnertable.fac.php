<?php

// Send table header.
echo "<tr><th>Staffel</th><th>UID</th><th>Vorname</th><th>Nachname</th><th>Aktionen</th></tr>";

// Check for authorisation. Minimum required permissionlevel: manager.
session_start();
if (!isset($_SESSION["permissionlevel"])) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\">Login benötigt.</td></tr>";
    exit;
}
$permission_level = $_SESSION["permissionlevel"];
require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($permission_level < $_PERMISSION_LEVELS["manager"]) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\">Fehlende Berechtigung.</td></tr>";
    exit;
}

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Run SQL query.
$sql_query =
    "SELECT
    rn.id AS runnerid,
    rn.firstname AS firstname,
    rn.surname AS surname,
    rn.runneruid AS runneruid,
    rl.name AS relayname
FROM
    runners as rn
INNER JOIN
    relays as rl
    ON (rl.id = rn.relayid)
WHERE
    rl.runid = ?
ORDER BY
    rl.name ASC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"5\" style=\"text-align: center;\"><i>Es sind keine Läufer im System registriert.</i></td></tr>";
    exit;
}

// Construct table html.
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $runner_firstname = $sql_row["firstname"];
    $runner_surname = $sql_row["surname"];
    $runner_uid = $sql_row["runneruid"];
    $relay_name = $sql_row["relayname"];

    $table_html = "<tr><td>"
        . $relay_name
        . "</td><td>"
        . $runner_uid
        . "</td><td>"
        . $runner_firstname
        . "</td><td>"
        . $runner_surname
        . "</td><td><ul><li><a onclick=\"delete_runner_click("
        . $sql_row["runnerid"]
        . ");\" href=\"#\">Löschen</a></li><li><a onclick=\"edit_runner_click("
        . $sql_row["runnerid"]
        . ");\" href=\"#\">Bearbeiten</a></li></ul></td></tr>";

    echo $table_html;
}

// Close database connection.
mysqli_close($db_conn);
