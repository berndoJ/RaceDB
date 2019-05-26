<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requireadmin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
    <title>RaceDB - Benutzermanagement</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/no_link_style.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js" />
    </script>
</head>

<body>

    <?php
    // Website Header
    include __DIR__ . "/header.php";

    include __DIR__ . "/includes/logout_ribbon.php";

    ?>

    <section id="user_management_section">
        <div class="stdcontainer">

            <?php
            if (isset($_GET["error"])) {
                ?>
                <div class="errorbox">
                    <h3>Fehler</h3>
                    <p>
                        <?php
                        switch ($_GET["error"]) {
                            case "delete_failed":
                                echo "Der Löschvorgang konnte nicht durchgeführt werden.";
                                break;
                            case "edit_failed":
                                echo "Der Benutzer konnte nicht bearbeitet werden.";
                                break;
                            default:
                                echo "Unbekannte Information.";
                                break;
                        }
                        ?>
                    </p>
                </div>
            <?php
        }
        ?>

            <?php
            if (isset($_GET["success"])) {
                ?>
                <div class="successbox">
                    <h3>Information</h3>
                    <p>
                        <?php
                        switch ($_GET["success"]) {
                            case "delete":
                                echo "Der Benutzer wurde gelöscht.";
                                break;
                            case "edit":
                                echo "Der Benutzer wurde erfolgreich bearbeitet.";
                                break;
                            default:
                                echo "Unbekannte Information.";
                                break;
                        }
                        ?>
                    </p>
                </div>
            <?php
        }
        ?>

            <h3>Benutzer</h3>
            <table class="deftable">
                <tr>
                    <th>
                        Benutzername
                    </th>
                    <th>
                        Berechtigungen
                    </th>
                    <th style="width: 300px;">
                        Aktionen
                    </th>
                </tr>

                <?php
                // Generate the usertable.
                require_once __DIR__ . "/includes/db.inc.php";
                $db_conn = open_db_connection();
                if (!$db_conn) {
                    echo "SQL connection error while loading users.";
                } else {
                    $sql_query = "SELECT * FROM users ORDER BY username;";
                    $sql_stmt = mysqli_stmt_init($db_conn);
                    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
                        echo "Bad user request SQL query.";
                    } else {
                        mysqli_stmt_execute($sql_stmt);
                        $sql_result = mysqli_stmt_get_result($sql_stmt);
                        while ($sql_row = mysqli_fetch_assoc($sql_result)) {
                            $permission_level = $sql_row["permissionlevel"];
                            $permission_level_str = "N.A.";
                            $permission_level_map =
                                [
                                    0 => "Nutzer",
                                    1 => "Managementbenutzer",
                                    10000 => "Administrator"
                                ];
                            if (isset($permission_level_map[$permission_level])) {
                                $permission_level_str = $permission_level_map[$permission_level];
                            }

                            $table_row_html = "<tr><td>"
                                . $sql_row["username"]
                                . "</td><td>"
                                . $permission_level_str
                                . "</td><td>";


                            if ($_SESSION["userid"] != $sql_row["id"]) {
                                $table_row_html .=
                                    "<ul><li><a onclick=\"delete_user_click("
                                    . $sql_row["id"]
                                    . ");\" href=\"#\">Löschen</a></li><li><a onclick=\"edit_user_click("
                                    . $sql_row["id"]
                                    . ");\" href=\"#\">Bearbeiten</a></li></ul>";
                            }

                            $table_row_html .= "</td></tr>";

                            echo $table_row_html;
                        }
                        mysqli_free_result($sql_result);
                    }
                    mysqli_close($db_conn);
                }
                ?>

                <script type="application/javascript">
                    function edit_user_click(user_id) {
                        edit_user_id = user_id;
                        $("#dialog_edit_user").show();
                        $.get("actions/get_username.act.php", {
                            userid: edit_user_id
                        }, function(data) {
                            $("#dialog_edit_user_username").text(data);
                        });
                        $.get("actions/get_user_permissionlevel.inc.php", {
                            userid: edit_user_id,
                            text: "set"
                        }, function(data) {
                            $("#dialog_edit_user_perm_sel option").filter(function() {
                                return $(this).val() == data;
                            }).prop("selected", true);
                        });
                    }

                    function delete_user_click(user_id) {
                        delete_user_id = user_id;
                        $("#dialog_delete_confirm").show();
                        $.get("actions/get_username.act.php", {
                            userid: delete_user_id
                        }, function(data) {
                            $("#dialog_delete_confirm_username").text(data);
                        });
                    }
                </script>
            </table>
        </div>
    </section>

    <!--Dialog for user delete confirm-->
    <div class="defmodal" id="dialog_delete_confirm">

        <!--Script for handling clicking the buttons on the dialog-->
        <script type="application/javascript">
            $(window).on("load", function() {
                // Close button
                $("#dialog_delete_confirm_closebutton").click(function() {
                    $("#dialog_delete_confirm").hide();
                });
                // Click outside the dialog bounds
                $("#dialog_delete_confirm").click(function() {
                    $("#dialog_delete_confirm").hide();
                }).children().click(function(e) {
                    e.stopPropagation();
                });
                // Cancel button
                $("#dialog_delete_confirm_cancelbutton").click(function() {
                    $("#dialog_delete_confirm").hide();
                });
                // Delete button
                $("#dialog_delete_confirm_deletebutton").click(function() {
                    $('<form action=\"actions/delete_user.act.php\" method=\"post\"><input type=\"hidden\" name=\"userid\" value=\"' + delete_user_id + '\"/></form>').appendTo('body').submit();
                });
            });
        </script>

        <div class="defmodal-content">
            <div class="defmodal-header">
                <span class="defmodal-closebtn" id="dialog_delete_confirm_closebutton">&times;</span>
                <h2>Benutzer löschen?</h2>
            </div>
            <div class="defmodal-body">
                <p>
                    Soll der Benutzer <b><span id="dialog_delete_confirm_username"></span></b> wirklich gelöscht werden?
                </p>
            </div>
            <div class="defmodal-footer">
                <ul class="horizontal_right_ul">
                    <li>
                        <button id="dialog_delete_confirm_deletebutton">Löschen</button>
                    </li>
                    <li>
                        <button id="dialog_delete_confirm_cancelbutton">Abbrechen</button>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <!--Dialog for user edit-->
    <div class="defmodal" id="dialog_edit_user">

        <!--Script for handling clicking the buttons on the dialog-->
        <script type="application/javascript">
            $(window).on("load", function() {
                // Close button
                $("#dialog_edit_user_closebutton").click(function() {
                    $("#dialog_edit_user").hide();
                });
                // Click outside the dialog bounds
                $("#dialog_edit_user").click(function() {
                    $("#dialog_edit_user").hide();
                }).children().click(function(e) {
                    e.stopPropagation();
                });
                // Cancel button
                $("#dialog_edit_user_cancelbutton").click(function() {
                    $("#dialog_edit_user").hide();
                });
                // Save button
                $("#dialog_edit_user_savebutton").click(function() {
                    permissionlevel_data = $("#dialog_edit_user_perm_sel").val();
                    if ($("#dialog_edit_user_passwd").val().trim()) {
                        if ($("#dialog_edit_user_passwd").val() == $("#dialog_edit_user_passwd_confirm").val()) {
                            password_data = $("#dialog_edit_user_passwd").val();
                            $('<form action=\"actions/edit_user.act.php\" method=\"post\"><input type=\"hidden\" name=\"userid\" value=\"' + edit_user_id + '\"/><input type=\"hidden\" name=\"password\" value=\"' + password_data + '\"/><input type=\"hidden\" name=\"permissionlevel\" value=\"' + permissionlevel_data + '\"/></form>').appendTo('body').submit();
                        }
                    } else {
                        $('<form action=\"actions/edit_user.act.php\" method=\"post\"><input type=\"hidden\" name=\"userid\" value=\"' + edit_user_id + '\"/><input type=\"hidden\" name=\"permissionlevel\" value=\"' + permissionlevel_data + '\"/></form>').appendTo('body').submit();
                    }
                });
            });
        </script>

        <div class="defmodal-content">
            <div class="defmodal-header">
                <span class="defmodal-closebtn" id="dialog_edit_user_closebutton">&times;</span>
                <h2>Benutzer bearbeiten</h2>
            </div>
            <div class="defmodal-body">
                <p>
                    Eigenschaften des Benutzers <b><span id="dialog_edit_user_username"></span></b> bearbeiten.<br />
                    Alle Felder, welche nicht ausgefüllt werden, werden nicht bearbeitet und der alte Wert wird beibehalten.
                </p>
                <p>
                    Neues Passwort<br />
                    <input type="password" id="dialog_edit_user_passwd" placeholder="Neues Passwort" />
                </p>
                <p>
                    Neues Passwort erneut eingeben<br />
                    <input type="password" id="dialog_edit_user_passwd_confirm" placeholder="Neues Passwort erneut eingeben" />
                </p>
                <p>
                    Benutzerrechte<br />
                    <select id="dialog_edit_user_perm_sel">
                        <option value="user">
                            Nutzer
                        </option>
                        <option value="manager">
                            Managementbenutzer
                        </option>
                        <option value="admin">
                            Administrator
                        </option>
                    </select>
                </p>
            </div>
            <div class="defmodal-footer">
                <ul class="horizontal_right_ul">
                    <li>
                        <button id="dialog_edit_user_savebutton">Speichern</button>
                    </li>
                    <li>
                        <button id="dialog_edit_user_cancelbutton">Abbrechen</button>
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