<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

if (!isset($_GET["runid"])) {
    echo "No run id given. Aborting edit of the run.";
} else {

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
            // * Transfering the runid variable contained in the request URL from
            // * the PHP server side to the js client side.
            const runid = <?php echo $_GET["runid"]; ?>;
        </script>
    </head>

    <body>

        <?php
        // Website Header
        include __DIR__ . "/header.php";
        include __DIR__ . "/includes/logout_ribbon.php";

        // Notification Lib
        include __DIR__ . "/notifications.php";

        ?>

        <section id="main_section">
            <div class="stdcontainer">
                <h1>
                    <?php
                    require_once __DIR__ . "/includes/utils.inc.php";
                    echo db_get_runname_from_id($_GET["runid"]);
                    ?>
                </h1>

                <h3>Staffeln</h3>
                <!--Script for relays-->
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_relay_table();

                        $("#button_newrelay").click(function() {
                            $("#dialog_create_relay_createbutton").prop("disabled", ($("#dialog_create_relay_name").val().trim().length == 0));
                            $("#dialog_create_relay_name").val("");
                            $("#dialog_create_relay").show();
                        });
                    });

                    function update_relay_table() {
                        $.get("html_factories/relaytable.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#relaytable").html(data);
                        });
                    }
                </script>
                <button id="button_newrelay" style="margin: 0px 0px 10px 0px;">Neue Staffel</button>
                <table class="deftable" id="relaytable"></table>

                <h3>Läuferliste</h3>
                <!--Script for runners-->
                <script type="application/javascript">
                    $(window).on("load", function() {
                        update_runner_table();

                        $("#button_newrunner").click(function() {
                            // Prepare dialog
                            $("#dialog_create_runner_createbutton").prop("disabled", true);
                            $("#dialog_create_runner_firstname").val("");
                            $("#dialog_create_runner_surname").val("");
                            $("#dialog_create_runner_uid").val("");

                            // Load relay select list
                            $.get("html_factories/relayselect.fac.php", {
                                runid: runid
                            }, function(data) {
                                $("#dialog_create_runner_relay").html(data);
                            });
                            $("#dialog_create_runner_createbutton").prop("disabled", (!$("#dialog_create_runner_relay").val()));

                            $("#dialog_create_runner").show();
                        });
                    });

                    function update_runner_table() {
                        $.get("html_factories/runnertable.fac.php", {
                            runid: runid
                        }, function(data) {
                            $("#runnertable").html(data);
                        });
                    }
                </script>
                <button id="button_newrunner" style="margin: 0px 0px 10px 0px;">Neuer Läufer</button>
                <table class="deftable" id="runnertable"></table>

            </div>
        </section>

        <!--Dialog for creating a new relay-->
        <div class="defmodal" id="dialog_create_relay">

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_create_relay_closebutton").click(function() {
                        $("#dialog_create_relay").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_create_relay").click(function() {
                        $("#dialog_create_relay").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_create_relay_cancelbutton").click(function() {
                        $("#dialog_create_relay").hide();
                    });
                    // Attach change script for validating the user's input.
                    $("#dialog_create_relay_name").on("input", function() {
                        $("#dialog_create_relay_createbutton").prop("disabled", ($(this).val().trim().length == 0));
                    });
                    // Attach enter keyup in input field to exec_create_relay() function.
                    $("#dialog_create_relay_name").keydown(function(e) {
                        if (e.keyCode == 13) {
                            exec_create_relay();
                        }
                    });
                    // Create button
                    $("#dialog_create_relay_createbutton").click(function() {
                        exec_create_relay();
                    });

                    // Function submitting create.
                    function exec_create_relay() {
                        relayname = $("#dialog_create_relay_name").val();
                        if (relayname.trim().length == 0) {
                            return;
                        }
                        $.post("actions/create_relay.act.php", {
                            runid: runid,
                            name: relayname
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Die Staffel " + data_sec[1] + " wurde erfolgreich erstellt.");
                                        break;
                                    case "ERR_EXISTS":
                                        display_notification("error", "Es existiert bereits eine Staffel mit dem Namen " + data_sec[1] + ".");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_relay_table();
                                $("#dialog_create_relay").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_create_relay_closebutton">&times;</span>
                    <h2>Neue Staffel hinzufügen</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Staffelname<br />
                        <input type="text" id="dialog_create_relay_name" placeholder="Name der Staffel" />
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_create_relay_createbutton">Hinzufügen</button>
                        </li>
                        <li>
                            <button id="dialog_create_relay_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!--Dialog for editing a relay-->
        <div class="defmodal" id="dialog_edit_relay">

            <!--Script for handling "Edit" button clicks in the action column of the relay table-->
            <script type="application/javascript">
                function edit_relay_click(id) {
                    edit_relay_id = id;

                    $.get("actions/get_relayname.act.php", {
                        relayid: id
                    }, function(data) {
                        $("#dialog_edit_relay_name").val(data);
                    });

                    $("#dialog_edit_relay").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_edit_relay_closebutton").click(function() {
                        $("#dialog_edit_relay").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_edit_relay").click(function() {
                        $("#dialog_edit_relay").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_edit_relay_cancelbutton").click(function() {
                        $("#dialog_edit_relay").hide();
                    });
                    // Attach change script for validating the user's input.
                    $("#dialog_edit_relay_name").on("input", function() {
                        $("#dialog_edit_relay_savebutton").prop("disabled", ($(this).val().trim().length == 0));
                    });
                    // Attach enter keyup in input field to exec_save_relay() function.
                    $("#dialog_edit_relay_name").keydown(function(e) {
                        if (e.keyCode == 13) {
                            exec_save_relay();
                        }
                    });
                    // Create button
                    $("#dialog_edit_relay_savebutton").click(function() {
                        exec_save_relay();
                    });

                    // Function submitting create.
                    function exec_save_relay() {
                        relayname = $("#dialog_edit_relay_name").val().trim();
                        $.post("actions/edit_relay.act.php", {
                            relayid: edit_relay_id,
                            name: relayname
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Die Änderungen wurden erfolgreich gespeichert.");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_relay_table();
                                $("#dialog_edit_relay").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_edit_relay_closebutton">&times;</span>
                    <h2>Staffel bearbeiten</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Staffelname<br />
                        <input type="text" id="dialog_edit_relay_name" placeholder="Name der Staffel" />
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_edit_relay_savebutton">Speichern</button>
                        </li>
                        <li>
                            <button id="dialog_edit_relay_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!--Dialog for deleting a relay-->
        <div class="defmodal" id="dialog_delete_relay">

            <!--Script for handling "Delete" button clicks in the action column of the relay table-->
            <script type="application/javascript">
                function delete_relay_click(id) {
                    delete_relay_id = id;

                    $.get("actions/get_relayname.act.php", {
                        relayid: id
                    }, function(data) {
                        $("#dialog_delete_relay_relayname").html(data);
                    });

                    $("#dialog_delete_relay").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_delete_relay_closebutton").click(function() {
                        $("#dialog_delete_relay").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_delete_relay").click(function() {
                        $("#dialog_delete_relay").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_delete_relay_cancelbutton").click(function() {
                        $("#dialog_delete_relay").hide();
                    });
                    // Delete button
                    $("#dialog_delete_relay_deletebutton").click(function() {
                        exec_delete_relay();
                    });

                    // Function confirming the deletion of the relay.
                    function exec_delete_relay() {
                        $.post("actions/delete_relay.act.php", {
                            relayid: delete_relay_id
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Die Staffel wurde erfolgreich gelöscht!");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_relay_table();
                                update_runner_table();
                                $("#dialog_delete_relay").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_delete_relay_closebutton">&times;</span>
                    <h2>Staffel löschen?</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Sollen die Staffel <b><span id="dialog_delete_relay_relayname"></span></b> und alle ihr zugeordneten Läufer wirklich permanent gelöscht werden?
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_delete_relay_deletebutton">Löschen</button>
                        </li>
                        <li>
                            <button id="dialog_delete_relay_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!--Dialog for creating a new runner-->
        <div class="defmodal" id="dialog_create_runner">

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_create_runner_closebutton").click(function() {
                        $("#dialog_create_runner").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_create_runner").click(function() {
                        $("#dialog_create_runner").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_create_runner_cancelbutton").click(function() {
                        $("#dialog_create_runner").hide();
                    });
                    // Attach change scripts for validating the user's input.
                    $("#dialog_create_runner_firstname").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_create_runner_surname").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_create_runner_uid").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_create_runner_relay").change(function() {
                        validate_input();
                    });

                    function validate_input() {
                        validated = validate_input_bool();

                        $("#dialog_create_runner_createbutton").prop("disabled", !validated);
                    }

                    function validate_input_bool() {
                        b = ($("#dialog_create_runner_firstname").val().trim().length == 0) ||
                            ($("#dialog_create_runner_surname").val().trim().length == 0) ||
                            ($("#dialog_create_runner_uid").val().trim().length == 0) ||
                            (!$("#dialog_create_runner_relay").val());

                        return !b;
                    }
                    // Create button
                    $("#dialog_create_runner_createbutton").click(function() {
                        exec_create_runner();
                    });

                    // Function submitting create.
                    function exec_create_runner() {
                        if (!validate_input_bool()) {
                            return;
                        }

                        firstname = $("#dialog_create_runner_firstname").val();
                        surname = $("#dialog_create_runner_surname").val();
                        runneruid = $("#dialog_create_runner_uid").val();
                        relayid = $("#dialog_create_runner_relay").val();

                        $.post("actions/create_runner.act.php", {
                            relayid: relayid,
                            firstname: firstname,
                            surname: surname,
                            runneruid: runneruid
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Läufer " + data_sec[1] + " " + data_sec[2] + " wurde erfolgreich hinzugefügt.");
                                        break;
                                    case "ERR_EXISTS":
                                        display_notification("error", "Es existiert bereits ein Läufer mit dem Namen " + data_sec[1] + " " + data_sec[2] + " und der Läufer-UID " + data_sec[3] + ".");
                                        break;
                                    case "ERR_UID_EXISTS":
                                        display_notification("error", "Es existiert bereits ein Läufer mit der angegebenen Läufer-UID. Diese muss innerhalb des Laufes eindeutig vergeben sein.");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_runner_table();
                                $("#dialog_create_runner").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_create_runner_closebutton">&times;</span>
                    <h2>Neuen Läufer hinzfügen</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Vorname<br />
                        <input type="text" id="dialog_create_runner_firstname" placeholder="Vorname" />
                    </p>
                    <p>
                        Nachname<br />
                        <input type="text" id="dialog_create_runner_surname" placeholder="Nachname" />
                    </p>
                    <p>
                        Referenz-UID<br />
                        <input type="text" id="dialog_create_runner_uid" placeholder="Referenz-UID" />
                        <i>Die Referenz-UID ist eine, vom Benutzer festgelegte, innerhalb des Laufes eindeutige Zuordnungsnummer / Zuordnungszeichenfolge. Diese wird benötigt, um den Läufer beim Lauf zu identifizieren.</i>
                    </p>
                    <p>
                        Staffel<br />
                        <select type="select" id="dialog_create_runner_relay"></select>
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_create_runner_createbutton">Hinzufügen</button>
                        </li>
                        <li>
                            <button id="dialog_create_runner_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!--Dialog for editing a runner-->
        <div class="defmodal" id="dialog_edit_runner">

            <!--Script for handling "Delete" button clicks in the action column of the relay table-->
            <script type="application/javascript">
                function edit_runner_click(id) {
                    edit_runner_id = id;

                    // Load relay select list
                    $.get("html_factories/relayselect.fac.php", {
                        runid: runid
                    }, function(data) {
                        $("#dialog_edit_runner_relay").html(data);
                    });

                    // Load the current info about the runner.
                    $.get("actions/get_runner_info.act.php", {
                        runnerid: edit_runner_id
                    }, function(data) {
                        data_sec = data.split("\n");

                        if (data_sec[0] != RESPONSE_SUCCESS) {
                            display_notification("error", "Die information über den gewählten Läufer konnte nicht vom Server abgefragt werden.");
                            return;
                        }

                        $("#dialog_edit_runner_firstname").val(data_sec[1]);
                        $("#dialog_edit_runner_surname").val(data_sec[2]);
                        $("#dialog_edit_runner_uid").val(data_sec[3]);
                        $("#dialog_edit_runner_relay").val(data_sec[4]);
                    });

                    $("#dialog_edit_runner").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_edit_runner_closebutton").click(function() {
                        $("#dialog_edit_runner").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_edit_runner").click(function() {
                        $("#dialog_edit_runner").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_edit_runner_cancelbutton").click(function() {
                        $("#dialog_edit_runner").hide();
                    });
                    // Attach change scripts for validating the user's input.
                    $("#dialog_edit_runner_firstname").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_edit_runner_surname").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_edit_runner_uid").on("input", function() {
                        validate_input();
                    });
                    $("#dialog_edit_runner_relay").change(function() {
                        validate_input();
                    });

                    function validate_input() {
                        validated = validate_input_bool();

                        $("#dialog_edit_runner_savebutton").prop("disabled", !validated);
                    }

                    function validate_input_bool() {
                        b = ($("#dialog_edit_runner_firstname").val().trim().length == 0) ||
                            ($("#dialog_edit_runner_surname").val().trim().length == 0) ||
                            ($("#dialog_edit_runner_uid").val().trim().length == 0) ||
                            (!$("#dialog_edit_runner_relay").val());

                        return !b;
                    }
                    // Create button
                    $("#dialog_edit_runner_savebutton").click(function() {
                        exec_save_runner();
                    });

                    // Function submitting save.
                    function exec_save_runner() {
                        if (!validate_input_bool()) {
                            return;
                        }

                        firstname = $("#dialog_edit_runner_firstname").val();
                        surname = $("#dialog_edit_runner_surname").val();
                        runneruid = $("#dialog_edit_runner_uid").val();
                        relayid = $("#dialog_edit_runner_relay").val();

                        $.post("actions/edit_runner.act.php", {
                            relayid: relayid,
                            firstname: firstname,
                            surname: surname,
                            runneruid: runneruid
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Läufer " + data_sec[1] + " " + data_sec[2] + " wurde erfolgreich hinzugefügt.");
                                        break;
                                    case "ERR_EXISTS":
                                        display_notification("error", "Es existiert bereits ein Läufer mit dem Namen " + data_sec[1] + " " + data_sec[2] + " und der Läufer-UID " + data_sec[3] + ".");
                                        break;
                                    case "ERR_UID_EXISTS":
                                        display_notification("error", "Es existiert bereits ein Läufer mit der angegebenen Läufer-UID. Diese muss innerhalb des Laufes eindeutig vergeben sein.");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_runner_table();
                                $("#dialog_edit_runner").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_edit_runner_closebutton">&times;</span>
                    <h2>Läufer bearbeiten</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Vorname<br />
                        <input type="text" id="dialog_edit_runner_firstname" placeholder="Vorname" />
                    </p>
                    <p>
                        Nachname<br />
                        <input type="text" id="dialog_edit_runner_surname" placeholder="Nachname" />
                    </p>
                    <p>
                        Referenz-UID<br />
                        <input type="text" id="dialog_edit_runner_uid" placeholder="Referenz-UID" />
                        <i>Die Referenz-UID ist eine, vom Benutzer festgelegte, innerhalb des Laufes eindeutige Zuordnungsnummer / Zuordnungszeichenfolge. Diese wird benötigt, um den Läufer beim Lauf zu identifizieren.</i>
                    </p>
                    <p>
                        Staffel<br />
                        <select type="select" id="dialog_edit_runner_relay"></select>
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_edit_runner_savebutton">Speichern</button>
                        </li>
                        <li>
                            <button id="dialog_edit_runner_cancelbutton">Abbrechen</button>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <!--Dialog for deleting a runner-->
        <div class="defmodal" id="dialog_delete_runner">

            <!--Script for handling "Delete" button clicks in the action column of the relay table-->
            <script type="application/javascript">
                function delete_runner_click(id) {
                    delete_runner_id = id;

                    $.get("actions/get_runner_info.act.php", {
                        runnerid: delete_runner_id
                    }, function(data) {
                        data_sec = data.split("\n");
                        if (data_sec[0] != RESPONSE_SUCCESS) {
                            return;
                        }
                        $("#dialog_delete_runner_name").html(data_sec[1] + " " + data_sec[2]);
                    });

                    $("#dialog_delete_runner").show();
                }
            </script>

            <!--Script for handling clicking the buttons on the dialog-->
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Close button
                    $("#dialog_delete_runner_closebutton").click(function() {
                        $("#dialog_delete_runner").hide();
                    });
                    // Click outside the dialog bounds
                    $("#dialog_delete_runner").click(function() {
                        $("#dialog_delete_runner").hide();
                    }).children().click(function(e) {
                        e.stopPropagation();
                    });
                    // Cancel button
                    $("#dialog_delete_runner_cancelbutton").click(function() {
                        $("#dialog_delete_runner").hide();
                    });
                    // Delete button
                    $("#dialog_delete_runner_deletebutton").click(function() {
                        exec_delete_runner();
                    });

                    // Function confirming the deletion of the relay.
                    function exec_delete_runner() {
                        $.post("actions/delete_runner.act.php", {
                            runnerid: delete_runner_id
                        }, function(data, status) {
                            if (status != "success") {
                                display_notification("error", NOTIFICATION_NO_CONNECTION);
                                return;
                            } else {
                                data_sec = data.split("\n");
                                switch (data_sec[0]) {
                                    case RESPONSE_SUCCESS:
                                        display_notification("success", "Der Läufer wurde entfernt!");
                                        break;
                                    case RESPONSE_ERROR_SQL_NO_CONNECTION:
                                        display_notification_default(NOTIFICATION_NO_SQL_CONNECTION);
                                        break;
                                    default:
                                        display_notification_default(NOTIFICATION_UNKNOWN_RESPONSE);
                                        console.log("Invalid response:\n" + data);
                                        break;
                                }
                                update_runner_table();
                                $("#dialog_delete_runner").hide();
                            }
                        });
                    }
                });
            </script>

            <div class="defmodal-content">
                <div class="defmodal-header">
                    <span class="defmodal-closebtn" id="dialog_delete_runner_closebutton">&times;</span>
                    <h2>Staffel löschen?</h2>
                </div>
                <div class="defmodal-body">
                    <p>
                        Soll der Läufer <b><span id="dialog_delete_runner_name"></span></b> permanent vom Lauf entfernt werden?
                    </p>
                </div>
                <div class="defmodal-footer">
                    <ul class="horizontal_right_ul">
                        <li>
                            <button id="dialog_delete_runner_deletebutton">Löschen</button>
                        </li>
                        <li>
                            <button id="dialog_delete_runner_cancelbutton">Abbrechen</button>
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

<?php
}
?>