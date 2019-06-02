<?php

// Authorise
require_once __DIR__ . "/../includes/msg.inc.php";

session_start();

if (!isset($_SESSION["username"])) {
    exit(MSG_ERROR_NOT_LOGGED_IN);
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["user"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

// Check for "runid" and "relayid" variable.
if (!isset($_GET["runid"]) || !isset($_GET["relayid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    exit(MSG_ERROR_SQL_NO_CONNECTION);
}

// Get all runners.
require_once __DIR__ . "/../includes/utils.inc.php";
$sql_query = "SELECT runners.runneruid AS rid, runners.firstname AS rf, runners.surname AS rs, runners.relayid AS rlid, relays.name AS rname FROM runners INNER JOIN relays ON (relays.id = runners.relayid) WHERE runners.relayid IN (SELECT id FROM relays WHERE runid = ?);";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    exit(MSG_ERROR_SQL_BAD_QUERY);
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

$csv = "Rang;Vorname;Nachname;Läufer-UID;Staffel;Zeit;\n";

$num = 0;
foreach ($runner_times as $rt) {
    $num++;

    if ($rt["rlid"] != $_GET["relayid"]) {
        continue;
    }

    $csv .= $num . ";" . $rt["rf"] . ";" . $rt["rs"] . ";" . $rt["rid"] . ";" . $rt["rname"] . ";";

    if ($rt["time"]  != null) {
        $csv .= utils_ms_to_format_str($rt["time"]) . ";";
    } else {
        $csv .= "Nicht teilgenommen.";
    }

    $csv .= "\n";
}

header("Content-Type: text/comma-separated-values; charset=UTF-8");
header("Content-disposition: attachment;filename=eval_table_all_runners.csv");
echo $csv;


// Close database connection.
mysqli_close($db_conn);
