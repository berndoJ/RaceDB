<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
    <title>RaceDB - Läufe und Staffeln bearbeiten</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/no_link_style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script type="application/javascript" src="./js/std_server_responses.js"></script>
    <script type="application/javascript" src="./js/std_notif_messages.js"></script>
</head>

<body>

    <?php
    // Website Header
    include __DIR__ . "/header.php";
    include __DIR__ . "/includes/logout_ribbon.php";

    include __DIR__ . "/notifications.php";

    ?>

    <section id="run_section">
        <div class="stdcontainer">
            <h3>Läufe</h3>
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Click on the create run button
                    $("#button_create_run").click(function() {
                        $("#dialog_create_run_name").val("");
                        $("#dialog_create_run_createbutton").prop("disabled", ($(this).val().trim().length == 0));
                        $("#dialog_create_run").show();
                    });
                    // Click on the deactivate all runs button
                    $("#button_deactivate_all_runs").click(function() {
                        $.post("actions/activate_run.act.php", {
                            runid: -1
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                update_runtable();
                            }
                        });
                    });
                });
            </script>
            <ul class="horizontal_ul">
                <button id="button_create_run" style="margin: 10px 5px 10px 0px;">Neuer Lauf</button>
                <button id="button_deactivate_all_runs" style="margin: 10px 5px 10px 0px;">Alle Läufe Deaktivieren</button>
            </ul>

            <!--Runtable script-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    update_runtable();
                });

                function update_runtable() {
                    $.get("html_factories/runtable.fac.php", {}, function(data) {
                        $("#runtable").html(data);
                    });
                }

                function delete_run_click(runid) {
                    delete_run_id = runid;
                    $("#dialog_delete_run_confirm").show();
                    $.get("actions/get_runname.act.php", {
                        runid: delete_run_id
                    }, function(data) {
                        $("#dialog_delete_run_confirm_runname").text(data);
                    });
                }

                function edit_run_click(runid) {
                    window.location.href = "edit_run.php?runid=" + runid;
                }

                function activate_run_click(runid) {
                    $.post("actions/activate_run.act.php", {
                        runid: runid
                    }, function(data, status) {});
                    window.location.href = "edit_runs.php";
                }
            </script>

            <!--Runtable-->
            <table class="deftable" id="runtable">
            </table>
        </div>
    </section>

    <!--Dialog for creating a new run-->
    <div class="defmodal" id="dialog_create_run">

        <!--Script for handling clicking the buttons on the dialog-->
        <script type="application/javascript">
            $(window).on("load", function() {
                // Close button
                $("#dialog_create_run_closebutton").click(function() {
                    $("#dialog_create_run").hide();
                });
                // Click outside the dialog bounds
                $("#dialog_create_run").click(function() {
                    $("#dialog_create_run").hide();
                }).children().click(function(e) {
                    e.stopPropagation();
                });
                // Cancel button
                $("#dialog_create_run_cancelbutton").click(function() {
                    $("#dialog_create_run").hide();
                });
                // Attach change script for validating the user's input.
                $("#dialog_create_run_name").on("input", function() {
                    $("#dialog_create_run_createbutton").prop("disabled", ($(this).val().trim().length == 0));
                });
                // Create button
                $("#dialog_create_run_createbutton").click(function() {
                    runname = $("#dialog_create_run_name").val();
                    if (runname.trim().length == 0) {
                        return;
                    }
                    $.post("actions/create_run.act.php", {
                        name: runname
                    }, function(data, status) {
                        if (status != "success") {
                            display_notification("error", NOTIFICATION_NO_CONNECTION);
                            return;
                        } else {
                            data_sec = data.split("\n");
                            switch (data_sec[0]) {
                                case RESPONSE_SUCCESS:
                                    display_notification("success", "Der Lauf " + data_sec[1] + " wurde erfolgreich erstellt.");
                                    break;
                                case "ERR_NAME_TAKEN":
                                    display_notification("error", "Es existiert bereits ein Lauf mit dem Namen " + data_sec[1] + ".");
                                    break;
                                case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                    display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                    break;
                                default:
                                    display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                    console.log("Invalid response:\n" + data);
                                    break;
                            }
                            update_runtable();
                            $("#dialog_create_run").hide();
                        }
                    });
                });
            });
        </script>

        <div class="defmodal-content">
            <div class="defmodal-header">
                <span class="defmodal-closebtn" id="dialog_create_run_closebutton">&times;</span>
                <h2>Neuen Lauf hinzufügen</h2>
            </div>
            <div class="defmodal-body">
                <p>
                    Laufname<br />
                    <input type="text" id="dialog_create_run_name" placeholder="Name des Laufes" />
                </p>
            </div>
            <div class="defmodal-footer">
                <ul class="horizontal_right_ul">
                    <li>
                        <button id="dialog_create_run_createbutton">Hinzufügen</button>
                    </li>
                    <li>
                        <button id="dialog_create_run_cancelbutton">Abbrechen</button>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <!--Dialog for confirming the deletion of a run.-->
    <div class="defmodal" id="dialog_delete_run_confirm">

        <!--Script for handling clicking the buttons on the dialog-->
        <script type="application/javascript">
            $(window).on("load", function() {
                // Close button
                $("#dialog_delete_run_confirm_closebutton").click(function() {
                    $("#dialog_delete_run_confirm").hide();
                });
                // Click outside the dialog bounds
                $("#dialog_delete_run_confirm").click(function() {
                    $("#dialog_delete_run_confirm").hide();
                }).children().click(function(e) {
                    e.stopPropagation();
                });
                // Cancel button
                $("#dialog_delete_run_confirm_cancelbutton").click(function() {
                    $("#dialog_delete_run_confirm").hide();
                });
                // Delete confirm button
                $("#dialog_delete_run_confirm_deletebutton").click(function() {
                    $.post("actions/delete_run.act.php", {
                        runid: delete_run_id
                    }, function(data, status) {
                        if (status != "success") {
                            display_notification("error", NOTIFICATION_NO_CONNECTION);
                            return;
                        } else {
                            data_sec = data.split("\n");
                            switch (data_sec[0]) {
                                case RESPONSE_SUCCESS:
                                    display_notification("success", "Der Lauf wurde erfolgreich gelöscht.");
                                    break;
                                case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                    display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                    break;
                                default:
                                    display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                    console.log("Invalid response:\n" + data);
                                    break;
                            }
                            update_runtable();
                            $("#dialog_delete_run_confirm").hide();
                        }
                    });
                });
            });
        </script>

        <div class="defmodal-content">
            <div class="defmodal-header">
                <span class="defmodal-closebtn" id="dialog_delete_run_confirm_closebutton">&times;</span>
                <h2>Lauf löschen?</h2>
            </div>
            <div class="defmodal-body">
                <p>
                    Soll der Lauf <span id="dialog_delete_run_confirm_runname">LAUFNAME</span> wirklich <b>DAUERHAFT</b> gelöscht werden?
                </p>
            </div>
            <div class="defmodal-footer">
                <ul class="horizontal_right_ul">
                    <li>
                        <button id="dialog_delete_run_confirm_deletebutton">Löschen</button>
                    </li>
                    <li>
                        <button id="dialog_delete_run_confirm_cancelbutton">Abbrechen</button>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <?php

    include __DIR__ . "/footer.php";

    ?>

</body>

</html>