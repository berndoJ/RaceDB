<?php

include __DIR__ . "/includes/phpheader.inc.php";
include __DIR__ . "/includes/requirelogin.inc.php";

?>

<!DOCTYPE html>

<html>

<head>
  <title>RaceDB - Index</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/defstyle.css" />
  <link rel="stylesheet" href="./css/no_link_style.css" />
  <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
</head>

<body>

  <?php
  // Website Header
  include __DIR__ . "/header.php";

  include __DIR__ . "/includes/logout_ribbon.php";

  ?>

  <section id="info_section">
    <div class="stdcontainer">

      <div class="infobox">
        <p>RaceDB Version 2.2 (Copyright (c) 2019 by Johannes Berndorfer)</p>
        <p>Deutsche Softwareversion</p>
        <p>Source Code: <a href="https://github.com/berndoJ/RaceDB">GitHub</a></p>
      </div>
      <div class="stdbox">
        <p>Willkommen,
          <br />
          <?php
          echo " " . $_SESSION["username"];
          ?>
        </p>
        <p>Unterhalb können Einstellungen vorgenommen werden und verschiedene Mess-Clients aufgerufen werden.</p>
      </div>

    </div>
  </section>

  <section id="menu_section">
    <div class="stdcontainer">

      <a href="./settings.php">
        <div class="selectionbox">
          <p>
            <b>Administratoreinstellungen</b>
          </p>
        </div>
      </a>

      <a href="./edit_runs.php">
        <div class="selectionbox">
          <p>
            <b>Läufe und Staffeln bearbeiten</b>
          </p>
        </div>
      </a>

      <a href="./evaluation.php">
        <div class="selectionbox">
          <p>
            <b>Laufauswertung</b>
          </p>
        </div>
      </a>

      <p>Messungs-Clients</p>

      <a href="./client_start.php">
        <div class="selectionbox">
          <p>
            <b>Laufstart</b>
          </p>
        </div>
      </a>

      <a href="./client_stop.php">
        <div class="selectionbox">
          <p>
            <b>Zeitstopp</b>
          </p>
        </div>
      </a>

      <a href="./client_stop_acquisition.php">
        <div class="selectionbox">
          <p>
            <b>UID-Aufnahme</b>
          </p>
        </div>
      </a>

    </div>
  </section>

  <?php

  include __DIR__ . "/footer.php";

  ?>

</body>

</html>