<?php

/**
 * Upload action script that enables the upload of a file to set the runner
 * configuration.
 * 
 * Variables:
 * FILE[runnercfg], runid
 */

require_once __DIR__ . "/../includes/msg.inc.php";

session_start();

if (!isset($_SESSION["username"])) {
    exit(MSG_ERROR_NOT_LOGGED_IN);
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["user"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

// Check for variable runid
if (!isset($_POST["runid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

require_once __DIR__ . "/../includes/utils.inc.php";

$csv = file_get_contents($_FILES["runnercfg"]["tmp_name"]);

$csv_lines = explode("\n", $csv);

foreach ($csv_lines as $csv_line) {
    $csv_cols = explode(";", str_replace(["\n", "\r"], "", $csv_line));

    // Columns: firstname, lastname, runner-uid, relayname (4 columns)
    if (sizeof($csv_cols) < 4) {
        continue;
    }

    // Get relayid from relayname
    $relayid_result = db_get_relayid_from_name($csv_cols[3], $_POST["runid"]);
    $relayid_sec = explode("\n", $relayid_result);
    if ($relayid_sec[0] != MSG_SUCCESS) {
        continue;
    }
    $relayid = $relayid_sec[1];

    db_create_runner($relayid, $csv_cols[0], $csv_cols[1], $csv_cols[2]);
}

header("Location: ../index.php");
exit;
