<?php

/**
 * Action script to delete a relay start event
 * 
 * Variables:
 * relayid
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

if (!isset($_POST["relayid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$relayid = $_POST["relayid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_delete_relay_start($relayid);

exit($db_result);
