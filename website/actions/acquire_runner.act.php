<?php

/**
 * Action script that acquires a runner.
 * 
 * Variables:
 * runid, runneruid, utc
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

if (!isset($_POST["runid"])|| !isset($_POST["runneruid"]) || !isset($_POST["utc"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runid = $_POST["runid"];
$runneruid = $_POST["runneruid"];
$utc = $_POST["utc"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_acquire_runner_by_ruid($runneruid, $utc, $runid);

exit($db_result);
