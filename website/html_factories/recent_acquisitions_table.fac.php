<?php

// Send table header.
echo "<tr><th>Läufer-UID</th><th>Staffel</th><th>Aktionen</th></tr>";

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Run SQL query.
$sql_query =
    "SELECT
    stopacquisition.id AS evid,
    runners.runneruid AS ruid,
    relays.name AS rlname
FROM
    stopacquisition
INNER JOIN
    runners
    ON (runners.id = stopacquisition.runnerid)
INNER JOIN
    relays
    ON (relays.id = runners.relayid)
WHERE
    stopacquisition.runnerid 
    IN (
        SELECT
            runners.id
        FROM
            runners
        WHERE
            runners.relayid
            IN (
                SELECT
                    relays.id
                FROM
                    relays
                WHERE
                    relays.runid=?
            )
    )
ORDER BY
    stopacquisition.utc DESC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\"><i>Es wurde noch kein Läufer zugeordnet.</i></td></tr>";
    exit;
}

// Construct table html.
$cnt = 0;
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $cnt++;

    $event_id = $sql_row["evid"];
    $runner_uid = $sql_row["ruid"];
    $relay_name = $sql_row["rlname"];

    $table_html = "<tr><td>"
        . $runner_uid
        . "</td><td>"
        . $relay_name
        . "</td><td><ul><li><a onclick=\"delete_acquisition_event("
        . $event_id
        . ");\" href=\"#\">Löschen</a></li></ul></td></tr>";

    echo $table_html;

    if (cnt == 10) {
        break;
    }
}

// Close database connection.
mysqli_close($db_conn);
