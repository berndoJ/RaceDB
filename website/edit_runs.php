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
    <link rel="stylesheet" href="./css/custom_checkbox.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>

<body>

    <?php
    // Website Header
    include __DIR__ . "/header.php";
    include __DIR__ . "/includes/logout_ribbon.php";

    ?>

    <section id="run_section">
        <div class="stdcontainer">
            <h3>Läufe</h3>
            <script type="application/javascript">
                $(window).on("load", function() {
                    // Click on the create run button
                    $("#button_create_run").click(function() {

                    });
                });
            </script>
            <button id="button_create_run" style="float: right; margin: 10px 0px;">Neuer Lauf</button>
            <table class="deftable">
                <tr>
                    <th>
                        Laufname
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Aktionen
                    </th>
                </tr>
                <?php
                // Fetch all runs from the database and present them to the user.
                require_once __DIR__ . "/includes/db.inc.php";
                $db_conn = open_db_connection();
                if (!$db_conn) {
                    echo "<tr><td>SQL error.</td></tr>";
                } else {
                    $sql_query = "SELECT * FROM runs;";
                    $sql_stmt = mysqli_stmt_init($db_conn);
                    if (!mysqli_stmt_prepare($sql_stmt, $sql_query)) {
                        echo "<tr><td>SQL error.</td></tr>";
                    } else {
                        mysqli_stmt_execute($sql_stmt);
                        $sql_result = mysqli_stmt_get_result($sql_stmt);

                        if (mysqli_num_rows($sql_result) < 1) {
                            echo "<tr><td colspan=\"3\" style=\"text-align: center;\"><i>Es wurden keine Läufe erstellt.</i></td></tr>";
                        } else {
                            while ($sql_row = mysqli_fetch_assoc($sql_result)) {
                                $run_active = $sql_row["active"] ? "<b>Aktiv</b>" : "Inaktiv";

                                $table_html = "<tr><td>"
                                    . $sql_row["name"]
                                    . "</td><td>"
                                    . $run_active
                                    . "</td><td><ul><li><a onclick=\"delete_run_click("
                                    . $sql_row["id"]
                                    . ");\" href=\"#\">Löschen</a></li><li><a onclick=\"edit_run_click("
                                    . $sql_row["id"]
                                    . ");\" href=\"#\">Bearbeiten</a></li>";

                                if ($sql_row["active"] != true) {
                                    $table_html .=
                                        "<li><a onclick=\"activate_run_click("
                                        . $sql_row["id"]
                                        . ");\" href=\"#\">Aktivieren</a></li>";
                                }

                                $table_html .= "</ul></td></tr>";

                                echo $table_html;
                            }
                        }
                    }
                }
                ?>
                <script type="application/javascript">
                function delete_run_click(runid)
                {

                }
                function edit_run_click(runid)
                {
                    
                }
                </script>
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
                // Create button
                $("#dialog_create_run_createbutton").click(function() {
                    // TODO: Write js code for the createbutton
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

    <?php

    include __DIR__ . "/footer.php";

    ?>

</body>

</html>