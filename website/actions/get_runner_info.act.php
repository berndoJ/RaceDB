<?php

/**
 * Action script to get all information about the runner by the given runnerid.
 * 
 * Variables:
 * runnerid
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

if (!isset($_GET["runnerid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runnerid = $_GET["runnerid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_get_runner_info_from_id($runnerid);

exit($db_result);
