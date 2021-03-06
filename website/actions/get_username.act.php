<?php

session_start();

if (!isset($_SESSION["username"])) {
    exit;
}

require_once __DIR__ . "/../includes/permissionlevel.inc.php";
if ($_SESSION["permissionlevel"] < $_PERMISSION_LEVELS["manager"]) {
    exit;
}

if (!isset($_GET["userid"])) {
    echo "n.a.";
    exit;
}

require_once __DIR__ . "/../includes/utils.inc.php";
$username = db_get_username_from_id($_GET["userid"]);
echo $username;
exit;
