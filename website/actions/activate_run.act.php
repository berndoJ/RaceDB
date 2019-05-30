<?php

require_once __DIR__ . "/../includes/msg.inc.php";

session_start();

if (!isset($_SESSION["username"])) {
    exit(MSG_ERROR_NOT_LOGGED_IN);
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["manager"]) {
    exit(MSG_ERROR_INSUFFICIENT_PERMISSION);
}

if (!isset($_POST["runid"])) {
    exit(MSG_ERROR_VARIABLES_MISSING);
}

require_once __DIR__ . "/../includes/utils.inc.php";
db_set_run_active($_POST["runid"]);

exit(MSG_SUCCESS);
