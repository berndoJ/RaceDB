<?php

/**
 * Action script that adds a new stopevent.
 * 
 * Variables:
 * runid, utc
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

if (!isset($_POST["runid"]) || !isset($_POST["utc"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runid = $_POST["runid"];
$utc = $_POST["utc"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_add_stopevent($runid, $utc);

exit($db_result);
