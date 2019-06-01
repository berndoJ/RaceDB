<?php

/**
 * Action script to get the difference of the number of stop events and the
 * number of stop acquisition data entries.
 * 
 * Variables:
 * runid
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

if (!isset($_GET["runid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runid = $_GET["runid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_get_diff_stop_acqu($runid);

exit($db_result);
