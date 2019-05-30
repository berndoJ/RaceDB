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
                <h3>Läuferliste</h3>
            </div>
        </section>

        <?php

        include __DIR__ . "/footer.php";

        ?>

    </body>

    </html>

<?php
}
?>