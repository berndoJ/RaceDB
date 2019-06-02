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
        var last_reg_ruid = null;
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
                    <h3>Läufer-UID-Aufnahme Client</h3>
                    <p>
                        Client, welcher am Laufende platziert wird. Hier werden alle Läufer, welche durch das Ziel gelaufen sind, anhand ihrer Läufer-UIDs in das System aufgenommen.
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
                <table style="width: 100%">
                    <tr>
                        <td>
                            <h3>UID-Aufnahme</h3>
                            <p><i>Alle Läufer, welche über die Ziellinie gelaufen sind, müssen hier der Reihenfolge nach, in welcher sie durch das Ziel gelaufen sind, in das System aufgenommen werden.</i></p>
                            <p>
                                <b>Läufer-UID Aufnahme</b> <i>(Zum Absenden [Enter] drücken)</i><br />
                                <input type="text" class="acquisition_input" id="runner_uid_acquisition" placeholder="Läufer-UID" />
                                <script type="application/javascript">
                                    $(window).on("load", function() {
                                        $("#runner_uid_acquisition").keydown(function(e) {
                                            if (e.keyCode == 13) {
                                                exec_acquire();
                                            }
                                        });

                                        focus_acquisition_field();
                                    });

                                    function focus_acquisition_field() {
                                        $("#runner_uid_acquisition").focus();
                                    }

                                    function exec_acquire() {
                                        acqu_utc = Date.now();
                                        runneruid = $("#runner_uid_acquisition").val().trim();
                                        if (runneruid == "") {
                                            return;
                                        }

                                        $.post("actions/acquire_runner.act.php", {
                                            runid: runid,
                                            runneruid: runneruid,
                                            utc: acqu_utc
                                        }, function(data, status) {
                                            if (status != "success") {
                                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                                return;
                                            } else {
                                                data_sec = data.split("\n");
                                                switch (data_sec[0]) {
                                                    case RESPONSE_SUCCESS:
                                                        display_notification("success", "Der Läufer wurde ins System aufgenommen.", 2500);
                                                        last_reg_ruid = runneruid;
                                                        break;
                                                    case "ERR_INVALID_RUNNERUID":
                                                        display_notification("error", "Die angegebene Läufer-UID existiert nicht.");
                                                        break;
                                                    case "ERR_ALREADY_ACQUIRED":
                                                        display_notification("info", "Der angegebene Läufer wurde bereits ins System aufgenommen.");
                                                        break;
                                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                                        break;
                                                    default:
                                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                                        console.log("Invalid response:\n" + data);
                                                        break;
                                                }
                                                $("#runner_uid_acquisition").val("");
                                                update_recent_acquisitions_table();
                                                update_last_time_ctr();
                                                focus_acquisition_field();
                                            }
                                        });
                                    }
                                </script>
                            </p>
                        </td>
                        <td style="width: 30%">
                            <h3 style="float: right;">Zeit der letzten Aufnhame</h3>
                            <p>
                                <table style="width: 100%;">
                                    <tr>
                                        <td>
                                            <span style="font-size: 40px; float: right;">
                                                <span id="last_time_ctr_maj">--:--:--</span><span id="last_time_ctr_min" style="font-size: 18px;">.---</span>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span style="float: right;" id="last_time_ctr_ruid"></span>
                                        </td>
                                    </tr>
                                </table>
                            </p>
                            <script type="application/javascript">
                                function update_last_time_ctr() {
                                    if (last_reg_ruid == null) {
                                        return;
                                    }

                                    $.get("actions/get_runner_time.act.php", {
                                        runid: runid,
                                        runneruid: last_reg_ruid
                                    }, function(data) {
                                        data_sec = data.split("\n");
                                        if (data_sec[0] != RESPONSE_SUCCESS) {
                                            return;
                                        }
                                        console.log(data_sec[1]);
                                        maj = __ultc_ms_str(data_sec[1], true);
                                        min = __ultc_ms_str(data_sec[1], false);
                                        $("#last_time_ctr_maj").html(maj);
                                        $("#last_time_ctr_min").html("." + min);
                                        $("#last_time_ctr_ruid").html(last_reg_ruid);
                                    });
                                }

                                function __ultc_ms_str(s, t) {
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

                                    if (t == true) {
                                        return pad(hrs) + ':' + pad(mins) + ':' + pad(secs);
                                    } else {
                                        return pad(ms,3);
                                    }
                                }
                            </script>
                        </td>
                    </tr>
                </table>

                <h3>Nicht zugeordnete Stopeinträge</h3>
                <p id="diff_stopevents_acquired"></p>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_diff_stop_acqu();

                        setInterval(function() {
                            update_diff_stop_acqu();
                        }, 500);
                    });

                    function update_diff_stop_acqu() {
                        $.get("actions/get_diff_stop_acqu_ev.act.php", {
                            runid: runid
                        }, function(data) {
                            data_sec = data.split("\n");
                            if (data_sec[0] != RESPONSE_SUCCESS) {
                                $("#diff_stopevents_acquired").html("<b>" + data[0] + "</b>")
                            } else {
                                cnt = data_sec[1];
                                if (cnt > 0) {
                                    $("#diff_stopevents_acquired").attr("style", "color: #f4bf42;");
                                    $("#diff_stopevents_acquired").html("Momentan nicht zugeordnete Stoppeinträge: <b>" + cnt + "</b>");
                                } else if (cnt < 0) {
                                    $("#diff_stopevents_acquired").attr("style", "color: #c63221;");
                                    $("#diff_stopevents_acquired").html("Momentan nicht getstoppte Zuordnungen: <b>" + cnt + "</b>");
                                } else {
                                    $("#diff_stopevents_acquired").attr("style", "color: #21c62f;");
                                    $("#diff_stopevents_acquired").html("Alle Stoppeinträge wurden zugeordnet.");
                                }
                            }
                        });
                    }
                </script>

                <h3>Letzte Läufer-UID Aufnahmen</h3>
                <table class="deftable" id="recent_acquisitions_table"></table>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_recent_acquisitions_table();
                    });

                    function update_recent_acquisitions_table() {
                        $.get("html_factories/recent_acquisitions_table.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#recent_acquisitions_table").html(data);
                        });

                        update_diff_stop_acqu();
                    }
                </script>
            </div>
        </section>

        <!--Dialog for deleting an acquisition event-->
        <div class="defmodal" id="dialog_delete_ae">

            <!--Script for handling "Delete" button clicks in the action column of the recent acquisitions table-->
            <script type="application/javascript">
                function delete_acquisition_event(id) {
                    delete_acquisition_event_id = id;
                    $("#dialog_delete_ae").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_delete_ae_closebutton").click(function() {
                        $("#dialog_delete_ae").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_delete_ae").click(function() {
                        $("#dialog_delete_ae").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_delete_ae_cancelbutton").click(function() {
                        $("#dialog_delete_ae").hide();
                    });
                    // Delete button
                    $("#dialog_delete_ae_deletebutton").click(function() {
                        exec_delete_ac_event();
                    });

                    // Function confirming the deletion of the acquisition event.
                    function exec_delete_ac_event() {
                        $.post("actions/delete_acquisition_event.act.php", {
                            eventid: delete_acquisition_event_id
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Zuordnungseintrag wurde erfolgreich gelöscht!");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_last_time_ctr();
                                update_recent_acquisitions_table();
                                $("#dialog_delete_ae").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_delete_ae_closebutton">&times;</span>
                    <h2>Zuordnung löschen?</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Soll die Zuordnung gelöscht werden?
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_delete_ae_deletebutton">Löschen</button>
                        </li>
                        <li>
                            <button id="dialog_delete_ae_cancelbutton">Abbrechen</button>
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