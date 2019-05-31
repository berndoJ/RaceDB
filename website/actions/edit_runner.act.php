<?php

/**
 * Action script to edit a runner.
 * 
 * Variables:
 * runnerid, [fistname], [surname], [runneruid], [relayid]
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

if (!isset($_POST["runnerid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

$runnerid = $_POST["runnerid"];

$firstname = null;
if (isset($_POST["firstname"])) {
    $firstname = $_POST["firstname"];
}

$surname = null;
if (isset($_POST["surname"])) {
    $surname = $_POST["surname"];
}

$runneruid = null;
if (isset($_POST["runneruid"])) {
    $runneruid = $_POST["runneruid"];
}

$relayid = null;
if (isset($_POST["relayid"])) {
    $relayid = $_POST["relayid"];
}

require_once __DIR__ . "/../includes/utils.inc.php";
$db_result = db_modify_runner($runnerid, $firstname, $surname, $runneruid, $relayid);

exit($db_result);
