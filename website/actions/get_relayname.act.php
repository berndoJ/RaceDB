<?php

/**
 * Action script to get the name of the relay given by the relayid.
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
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["manager"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

if (!isset($_GET["relayid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$relayid = $_GET["relayid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_get_relayname_from_id($relayid);

if ($db_result == false)
    exit("n.a.");
else
    exit($db_result);
