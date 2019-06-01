<?php

// Send table header.
echo "<tr><th>Stoppzeit</th><th>Gestoppt Vor</th><th>Aktionen</th></tr>";

// Check for "runid" variable.
if (!isset($_GET["runid"])) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">Variable \"runid\" fehlt.</td></tr>";
    exit;
}

// Open database connection.
require_once __DIR__ . "/../includes/db.inc.php";
$db_conn = open_db_connection();
if (!$db_conn) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}

// Run SQL query.
$sql_query = "SELECT * FROM stopevents WHERE runid=? ORDER BY utc DESC;";
$sql_stmt = mysqli_stmt_init($db_conn);
if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\">SQL Fehler.</td></tr>";
    exit;
}
mysqli_stmt_bind_param($sql_stmt, "i", $_GET["runid"]);
mysqli_stmt_execute($sql_stmt);
$sql_result = mysqli_stmt_get_result($sql_stmt);

if (mysqli_num_rows($sql_result) < 1) {
    echo "<tr><td colspan=\"3\" style=\"text-align: center;\"><i>Es wurden noch keine Läufer gestoppt.</i></td></tr>";
    exit;
}

// Construct table html.
$count = 0;
while ($sql_row = mysqli_fetch_assoc($sql_result)) {
    $count++;
    $stop_utc = $sql_row["utc"];
    $ev_id = $sql_row["id"];

    $table_html = "<tr><td><span id=\"stop_label_"
        . $ev_id
        . "\"></span><script type=\"application/javascript\">$(\"#stop_label_"
        . $ev_id
        . "\").html((new Date("
        . $stop_utc
        . ")).toLocaleDateString(\"de-DE\", {weekday:\"short\",day:\"2-digit\",month:\"short\",year:\"numeric\",hour:\"2-digit\",minute:\"2-digit\",second:\"2-digit\"}));</script>"
        . "</td><td><span id=\"diff_label_"
        . $ev_id . $stop_utc
        . "\"></span><script type=\"application/javascript\">setInterval(function(){ $(\"#diff_label_"
        . $ev_id . $stop_utc
        . "\").html(__rstt_ms_t(Date.now()-"
        . $stop_utc
        . ")); }, 10);</script></td><td><ul><li><a onclick=\"delete_stop_click("
        . $ev_id
        . ");\" href=\"#\">Löschen</a></li></ul></td></tr>";

    echo $table_html;

    if ($count == 10) {
        break;
    }
}

// Inject time helper script.
echo "<script type=\"application/javascript\">function __rstt_ms_t(s) {

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
  
    return pad(hrs) + ':' + pad(mins) + ':' + pad(secs) + '.' + pad(ms,3);
  }</script>";

// Close database connection.
mysqli_close($db_conn);
