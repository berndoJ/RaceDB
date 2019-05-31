<?php

session_start();

if (!isset($_SESSION["username"])) {
    exit;
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["user"]) {
    exit;
}

if (!isset($_GET["runid"])) {
    echo "n.a.";
    exit;
}

require_once __DIR__."/../includes/utils.inc.php";
$runname = db_get_runname_from_id($_GET["runid"]);
echo $runname;
exit;
