<?php

/**
 * Action script to create a relay.
 * 
 * Variables:
 * runid, name
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

if (!isset($_POST["runid"]) || !isset($_POST["name"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runid = $_POST["runid"];
$relayname = $_POST["name"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_create_relay($runid, $relayname);

if ($db_result == MSG_SUCCESS || $db_result == "ERR_EXISTS")
    exit($db_result . "\n$relayname");
else
    exit($db_result);
