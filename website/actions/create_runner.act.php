<?php

/**
 * Action script to create a runner.
 * 
 * Variables:
 * relayid, firstname, surname, runneruid
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

if (!isset($_POST["relayid"]) || !isset($_POST["firstname"]) || !isset($_POST["surname"]) || !isset($_POST["runneruid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$relayid = $_POST["relayid"];
$firstname = $_POST["firstname"];
$surname = $_POST["surname"];
$uid = $_POST["runneruid"];

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_create_runner($relayid, $firstname, $surname, $uid);

if ($db_result == MSG_SUCCESS || $db_result == "ERR_EXISTS")
    exit($db_result . "\n$firstname\n$surname\n$uid");
else
    exit($db_result);
