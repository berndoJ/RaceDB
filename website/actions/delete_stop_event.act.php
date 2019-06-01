<?php

/**
 * Action script to delete a stop event
 * 
 * Variables:
 * eventid
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

if (!isset($_POST["eventid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$eventid = $_POST["eventid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_delete_stopevent($eventid);

exit($db_result);
