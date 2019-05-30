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
    <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>

<body>

    <?php
    // Website Header
    include __DIR__ . "/header.php";
    include __DIR__ . "/includes/logout_ribbon.php";

    ?>

    <section id="info_section">
        <div class="stdcontainer">
            <div class="stdbox">
                <h3>Laufstart-Client</h3>
                <p>
                    Client, welcher am Laufstart platziert wird. Hier werden Staffeln gestartet.
                </p>
            </div>
        </div>
    </section>

    <section id="client_section">
        <div class="stdcontainer">
            <form action="" method="post">
                <h3>Staffelauswahl</h3>
                <p>
                    Auswählen der Staffeln, welche gestartet werden sollen:
                </p>
                <div>

                    <?php

                    require_once __DIR__ . "/includes/checkboxfactory.inc.php";

                    generate_checkbox("cbc-1", "hi1");
                    generate_checkbox("cbc-1", "hi2");
                    generate_checkbox("cbc-1", "hi3");
                    generate_checkbox("cbc-1", "hi4");
                    generate_checkbox("cbc-1", "hi5");
                    generate_checkbox("cbc-1", "hi6");

                    ?>

                    <!-- <label class="ccb_container">STAFFEL1<input type="checkbox"/><span class="ccb_checkmark"></span></label> -->

                </div>
                <h3>Staffelstart</h3>
                <p>
                    Momentane Zeitkonstante:
                </p>
                <script type="application/javascript">
                    $(window).on("load", function() {
                        setInterval(function() {
                            $("#utc").text(Date.now());
                        }, 5);
                    });
                </script>
                <p style="font-size: 24px;" id="utc">JAVASCRIPT NOT ENABLED</p>
                <button type="submit" name="client-start-submit">Starten</button>
            </form>
        </div>
    </section>

    <?php

    include __DIR__ . "/footer.php";

    ?>

</body>

</html>