<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
    <title>RaceDB - Lauf Auswerten</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/no_link_style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="application/javascript" src="./js/std_server_responses.js"></script>
    <script type="application/javascript" src="./js/std_notif_messages.js"></script>
    <script type="application/javascript">
        const runid =
            <?php
            require_once __DIR__ . "/includes/utils.inc.php";
            $active_run = db_get_active_run();
            if ($active_run == null)
                echo "null";
            else
                echo $active_run;
            ?>;
    </script>
</head>

<body>

    <?php
    // Website Header
    include __DIR__ . "/header.php";
    include __DIR__ . "/includes/logout_ribbon.php";

    // Notification Lib
    include __DIR__ . "/notifications.php";

    if ($active_run == null) {
        ?>

        <section id="info_section">
            <div class="stdcontainer">
                <div class="infobox">
                    <h3>Information</h3>
                    <p>
                        Im Moment ist kein Lauf aktiv. Um einen Lauf auszuwertens, muss ein Administrator oder ein Manager einen Lauf aktivieren.
                    </p>
                </div>
            </div>
        </section>

    <?php
} else {
    ?>

        <section id="info_section">
            <div class="stdcontainer">
                <div class="stdbox">
                    <h3>Auswerten</h3>
                    <p>
                        Hier kann der aktuelle Lauf ausgewertet werden.
                    </p>
                    <p>
                        <b>Momentaner Lauf: </b>
                        <section id="active_run_name_label"></section>
                        <script type="application/javascript">
                            $(window).on("load", function() {
                                $.get("actions/get_runname.act.php", {
                                    runid: runid
                                }, function(data) {
                                    $("#active_run_name_label").html(data);
                                });
                            });
                        </script>
                    </p>
                </div>
            </div>
        </section>

        <section id="main_section">
            <div class="stdcontainer">
                <h3>Auswertungstabelle Auswählen</h3>
                <select id="eval_table_select">
                    <option value="all_runners">Alle Läuferzeiten</option>
                    <?php
                    // Open database connection.
                    require_once __DIR__ . "/includes/db.inc.php";
                    $db_conn = open_db_connection();
                    if ($db_conn) {
                        // Generate options
                        $sql_query = "SELECT * FROM relays WHERE runid=?;";
                        $sql_stmt = mysqli_stmt_init($db_conn);
                        if (mysqli_stmt_prepare($sql_stmt, $sql_query)) {
                            mysqli_stmt_bind_param($sql_stmt, "i", $active_run);
                            mysqli_stmt_execute($sql_stmt);
                            $sql_result = mysqli_stmt_get_result($sql_stmt);
                            while ($sql_row = mysqli_fetch_assoc($sql_result)) {
                                $opt_id = $sql_row["id"];
                                $opt_label = "Staffel " . $sql_row["name"];
                                echo "<option value=\"$opt_id\">$opt_label</option>";
                            }

                            // Close database connection and clear result space.
                            mysqli_free_result($sql_result);
                            mysqli_close($db_conn);
                        }
                    }
                    ?>
                </select>

                <h3>Auswertungstabelle</h3>
                <button id="download_table_btn" style="margin: 10px 0px;">Tabelle (CSV) herunterladen</button>
                <table class="deftable" id="eval_table"></table>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        $("#download_table_btn").click(function() {
                            download_table();
                        });

                        $("#eval_table_select").on("change", function(e) {
                            update_eval_table();
                        });

                        update_eval_table();
                    });

                    function update_eval_table() {
                        eval_table_selection = $("#eval_table_select").val();

                        switch (eval_table_selection) {
                            case "all_runners":
                                $.get("html_factories/eval_table_all_runners.fac.php", {
                                    runid: runid
                                }, function(data) {
                                    $("#eval_table").html(data);
                                });
                                break;
                            default:
                                if (isNaN(eval_table_selection)) {
                                    $("#eval_table").html("<b>Auswahlfehler.</b>");
                                } else {
                                    $.get("html_factories/eval_table_relay.fac.php", {
                                        runid: runid,
                                        relayid: eval_table_selection
                                    }, function(data) {
                                        $("#eval_table").html(data);
                                    });
                                }
                                break;
                        }
                    }

                    function download_table() {
                        eval_table_selection = $("#eval_table_select").val();

                        switch (eval_table_selection) {
                            case "all_runners":
                                window.location.href = "actions/download_eval_table_all_runners.act.php?runid=" + runid;
                                break;
                            default:
                                if (isNaN(eval_table_selection)) {
                                    display_notification("error", "Auswahlfehler. Die Datei kann nicht heruntergeladen werden.");
                                } else {
                                    window.location.href = "actions/download_eval_table_relay.act.php?runid=" + runid + "&relayid=" + eval_table_selection;
                                }
                                break;
                        }
                    }
                </script>
            </div>
        </section>

    <?php
}

include __DIR__ . "/footer.php";

?>

</body>

</html>