<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
    <title>RaceDB - Lauf Bearbeiten</title>
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
                        Im Moment ist kein Lauf aktiv. Um diesen Client zu verwenden, muss ein Administrator oder ein Manager einen Lauf aktivieren.
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
                    <h3>Laufstopp-Client</h3>
                    <p>
                        Client, welcher am Laufende platziert wird. Hier werden die individuellen Zeiten der Läufer gestoppt.
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
                <h3>Stoppen</h3>
                <p><i>Jedes mal, wenn ein Läufer über die Ziellinie läuft, muss einmal gestoppt werden. Um Fehler zu vermeiden, muss gesichert werden, dass für jeden Läufer, der über die Ziellinie gelaufen ist, genau einmal gestoppt wurde.</i></p>
                <button id="start_relays_button" class="tbutton" style="width: 100%; height: 100px; font-size: 30px;">Stopp</button>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        $("#start_relays_button").click(function() {
                            exec_stop_event();
                        });
                    });
                            
                    function exec_stop_event() {
                        stop_utc = Date.now();

                        $.post("actions/stop_time.act.php", {
                            runid: runid,
                            utc: stop_utc
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Läufer wurde gestoppt.", 2500);
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_recent_stop_table();
                            }
                        });
                    }
                </script>

                <h3>Nicht zugeordnete Stopeinträge</h3>
                <p><i>Momentan nicht zugeordnete Stoppeinträge: </i><b><span id="not_acquired_stopevents">JAVASCRIPT ERROR</span></b></p>

                <h3>Letzte Stopps</h3>
                <table class="deftable" id="recent_stop_table"></table>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_recent_stop_table();
                    });

                    function update_recent_stop_table() {
                        $.get("html_factories/recent_stop_table.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#recent_stop_table").html(data);
                        });
                    }
                </script>
            </div>
        </section>

        <!--Dialog for deleting a stop event-->
        <div class="defmodal" id="dialog_delete_stop">

            <!--Script for handling "Delete" button clicks in the action column of the recent stop table-->
            <script type="application/javascript">
                function delete_stop_click(id) {
                    delete_stop_id = id;
                    $("#dialog_delete_stop").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_delete_stop_closebutton").click(function() {
                        $("#dialog_delete_stop").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_delete_stop").click(function() {
                        $("#dialog_delete_stop").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_delete_stop_cancelbutton").click(function() {
                        $("#dialog_delete_stop").hide();
                    });
                    // Delete button
                    $("#dialog_delete_stop_deletebutton").click(function() {
                        exec_delete_stop();
                    });

                    // Function confirming the deletion of the stop event.
                    function exec_delete_stop() {
                        $.post("actions/delete_stop_event.act.php", {
                            eventid: delete_stop_id
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Stoppeintrag wurde erfolgreich gelöscht!");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_recent_stop_table();
                                $("#dialog_delete_stop").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_delete_stop_closebutton">&times;</span>
                    <h2>Stopeintrag löschen?</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Soll der Stopeintrag wirklich gelöscht werden?
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_delete_stop_deletebutton">Löschen</button>
                        </li>
                        <li>
                            <button id="dialog_delete_stop_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    <?php
}

include __DIR__ . "/footer.php";

?>

</body>

</html>