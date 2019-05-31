<?php

// Send table header.
echo "<tr><th>Staffelname</th><th>Startzeit</th><th>Gestartet Vor</th><th>Aktionen</th></tr>";

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<tr><td colspan=\"4\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"4\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Run SQL query.
$sql_query = "SELECT relays.id AS relayid, relays.name AS relayname, startevents.utc AS startutc FROM relays, startevents WHERE relays.runid=? AND startevents.relayid = relays.id AND relays.id IN (SELECT relayid FROM startevents) ORDER BY startutc DESC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"4\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"4\" style=\"text-align: center;\"><i>Es wurden keine Staffeln gestartet.</i></td></tr>";
    exit;
}

// Inject time helper script.
echo "<script type=\"application/javascript\">function __rst_ms_t(s) {

    // Pad to 2 or 3 digits, default is 2
    function pad(n, z) {
      z = z || 2;
      return ('00' + n).slice(-z);
    }
  
    var ms = s % 1000;
    s = (s - ms) / 1000;
    var secs = s % 60;
    s = (s - secs) / 60;
    var mins = s % 60;
    var hrs = (s - mins) / 60;
  
    return pad(hrs) + ':' + pad(mins) + ':' + pad(secs) + '.' + pad(ms);
  }</script>";

// Construct table html.
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $relay_id = $sql_row["relayid"];
    $relay_name = $sql_row["relayname"];
    $start_utc = $sql_row["startutc"];

    $table_html = "<tr><td>"
        . $relay_name
        . "</td><td><span id=\"start_label_"
        . $relay_id
        . "\"></span><script type=\"application/javascript\">$(\"#start_label_"
        . $relay_id
        . "\").html((new Date("
        . $start_utc
        . ")).toLocaleDateString(\"de-DE\", {weekday:\"short\",day:\"2-digit\",month:\"short\",year:\"numeric\",hour:\"2-digit\",minute:\"2-digit\",second:\"2-digit\"}));</script>"
        . "</td><td><span id=\"diff_label_"
        . $relay_id
        . "\"></span><script type=\"application/javascript\">setInterval(function(){ $(\"#diff_label_"
        . $relay_id
        . "\").html(__rst_ms_t(Date.now()-"
        . $start_utc
        . ")); }, 10);</script></td><td><ul><li><a onclick=\"delete_start_click("
        . $relay_id
        . ");\" href=\"#\">Löschen</a></li></ul></td></tr>";

    echo $table_html;
}

// Close database connection.
mysqli_close($db_conn);
