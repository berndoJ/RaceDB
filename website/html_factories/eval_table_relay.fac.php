<?php

// Send table header.
echo "<tr><th>Rang</th><th>Vorname</th><th>Nachname</th><th>Läufer-UID</th><th>Staffel</th><th>Zeit</th></tr>";

// Check for "runid" and "relayid" variable.
if (!isset($_GET["runid"]) || !isset($_GET["relayid"])) {
    echo "<tr><td colspan=\"6\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"6\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Get all runners.
require_once __DIR__ . "/../includes/utils.inc.php";
$sql_query = "SELECT runners.runneruid AS rid, runners.firstname AS rf, runners.surname AS rs, runners.relayid AS rlid, relays.name AS rname FROM runners INNER JOIN relays ON (relays.id = runners.relayid) WHERE runners.relayid IN (SELECT id FROM relays WHERE runid = ?);";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"6\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);
$runner_times = [];
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $sql_row_time = db_get_runner_time($sql_row["rid"], $_GET["runid"]);
    $a = explode("\n", $sql_row_time);
    if ($a[0] == "SUCCESS") {
        $sql_row["time"] = $a[1];
    } else {
        $sql_row["time"] = null;
    }
    $runner_times[] = $sql_row;
}

// Sort
function _sort_runner_times_cb($a, $b)
{
    if ($a["time"] == null && $b["time"] != null) {
        return 1;
    } else if ($b["time"] == null && $a["time"] != null) {
        return -1;
    } else if ($b["time"] == null && $a["time"] == null) {
        return 1;
    }
    return ($a["time"] >= $b["time"]) ? -1 : 1;
}
usort($runner_times, "_sort_runner_times_cb");

$num = 0;
foreach ($runner_times as $rt) {
    $num++;
    if ($rt["rlid"] != $_GET["relayid"]) {
        continue;
    }
    $table_html = "<tr><td>"
        . $num
        . "</td><td>"
        . $rt["rf"]
        . "</td><td>"
        . $rt["rs"]
        . "</td><td>"
        . $rt["rid"]
        . "</td><td>"
        . $rt["rname"]
        . "</td><td>";

    if ($rt["time"]  != null) {
        $table_html .= utils_ms_to_format_str($rt["time"]);
    } else {
        $table_html .= "<i>Nicht teilgenommen.</i>";
    }

    $table_html .= "</td></tr>";

    echo $table_html;
}



// Close database connection.
mysqli_close($db_conn);
