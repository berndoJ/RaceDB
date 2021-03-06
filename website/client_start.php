<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
    <title>RaceDB - Laufstart Client</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/custom_checkbox.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
    <link rel="stylesheet" href="./css/no_link_style.css" />
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

    // Notifications
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
                    <h3>Laufstart-Client</h3>
                    <p>
                        Client, welcher am Laufstart platziert wird. Hier werden Staffeln gestartet.
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

        <section id="client_section">
            <div class="stdcontainer">
                <h3>Staffelauswahl</h3>
                <p>
                    Auswählen der Staffeln, welche gestartet werden sollen:
                </p>
                <div id="relay_checkbox_div"></div>
                <!--Script for updating relay checkboxes.-->
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_relay_checkboxes();
                    });

                    function update_relay_checkboxes() {
                        $.get("html_factories/startrelaycheckboxes.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#relay_checkbox_div").html(data);
                        });
                        update_recent_start_table();
                    }
                </script>

                <h3>Staffelstart</h3>
                <p>
                    Momentane Zeitkonstante:
                </p>
                <!--Script for updating the time constant label on the site-->
                <script type="application/javascript">
                    $(window).on("load", function() {
                        setInterval(function() {
                            $("#utc").text(Date.now());
                        }, 5);
                    });
                </script>
                <p style="font-size: 24px;" id="utc">JAVASCRIPT NOT ENABLED</p>
                <button id="start_relays_button" class="tbutton" style="width: 100%; height: 100px; font-size: 30px;">Starten</button>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        $("#start_relays_button").click(function() {
                            exec_start_relays();
                        });
                    });

                    function exec_start_relays() {
                        start_utc = Date.now()

                        relay_checks = [];
                        $("#relay_checkbox_div .__RELAY_CHECKBOX").each(function() {
                            relay_checks.push([this.id, this.checked]);
                        });

                        for (i = 0; i < relay_checks.length; i++) {
                            el = relay_checks[i];
                            if (el[1] == false) {
                                continue;
                            }

                            $.post("actions/start_relay.act.php", {
                                relayid: el[0],
                                utc: start_utc
                            }, function(data, status) {
                                if (status != "success") {
                                    display_notification("error", NOTIFICATION_NO_CONNECTION);
                                    return;
                                } else {
                                    data_sec = data.split("\n");
                                    switch (data_sec[0]) {
                                        case RESPONSE_SUCCESS:
                                            display_notification("success", "Die Staffel wurde gestartet.");
                                            break;
                                            /*case "UTC_OUT_OF_SYNC":
                                                display_notification("error", "Die Zeit des Clients ist nicht mit der Serverzeit synchronisiert. Um mögliche Zeitdifferenzfehler zu vermeiden und Staffeln zu starten, muss die Clientzeit mit der Serverzeit synchronisiert werden. (UTC)");
                                                break;*/
                                        case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                            display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                            break;
                                        default:
                                            display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                            console.log("Invalid response:\n" + data);
                                            break;
                                    }
                                    update_relay_checkboxes();
                                }
                            });
                        }
                    }
                </script>
                <h3>Letzte Starts</h3>
                <script type="application/javascript">
                    function update_recent_start_table() {
                        $.get("html_factories/recent_start_table.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#recent_start_table").html(data);
                        });
                    }
                </script>
                <table class="deftable" id="recent_start_table"></table>
            </div>
        </section>

        <!--Dialog for deleting a relay start-->
        <div class="defmodal" id="dialog_delete_start">

            <!--Script for handling "Delete" button clicks in the action column of the recent starts table-->
            <script type="application/javascript">
                function delete_start_click(id) {
                    delete_start_id = id;

                    $.get("actions/get_relayname.act.php", {
                        relayid: id
                    }, function(data) {
                        $("#dialog_delete_start_relayname").html(data);
                    });

                    $("#dialog_delete_start").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_delete_start_closebutton").click(function() {
                        $("#dialog_delete_start").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_delete_start").click(function() {
                        $("#dialog_delete_start").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_delete_start_cancelbutton").click(function() {
                        $("#dialog_delete_start").hide();
                    });
                    // Delete button
                    $("#dialog_delete_start_deletebutton").click(function() {
                        exec_delete_start();
                    });

                    // Function confirming the deletion of the relay start.
                    function exec_delete_start() {
                        $.post("actions/delete_relay_start.act.php", {
                            relayid: delete_start_id
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Staffelstarteintrag wurde erfolgreich gelöscht!");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_relay_checkboxes(); // Updates both - the relay checkboxes and the recent start table.
                                $("#dialog_delete_start").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_delete_start_closebutton">&times;</span>
                    <h2>Staffelstart löschen?</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Soll der Starteintrag der Staffel <b><span id="dialog_delete_start_relayname"></span></b> wirklich gelöscht werden?
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_delete_start_deletebutton">Löschen</button>
                        </li>
                        <li>
                            <button id="dialog_delete_start_cancelbutton">Abbrechen</button>
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