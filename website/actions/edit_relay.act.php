<?php

/**
 * Action script to edit a relay.
 * 
 * Variables:
 * relayid, [name]
 */

require_once __DIR__ . "/../includes/msg.inc.php";

session_start();

if (!isset($_SESSION["username"])) {
    exit(MSG_ERROR_NOT_LOGGED_IN);
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["manager"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

if (!isset($_POST["relayid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$relayid = $_POST["relayid"];

$relayname = null;
if (isset($_POST["name"])) {
    $relayname = $_POST["name"];
}

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_modify_relay($relayid, $relayname);

exit($db_result);
