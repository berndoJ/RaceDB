<?php

/**
 * Action script that starts a relay with the given relayid and UTC-constant.
 * 
 * Variables:
 * relayid, utc
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

if (!isset($_POST["relayid"]) || !isset($_POST["utc"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$relayid = $_POST["relayid"];
$utc = $_POST["utc"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_start_relay($relayid, $utc);

exit($db_result);
