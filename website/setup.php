<?php

require_once __DIR__."/config/cfghandler.inc.php";
$config = retrieve_default_config();

if ($config->get("setup.complete") == TRUE)
{
  header("Location: ./login.php");
  exit();
}

?>

<!DOCTYPE html>

<html>

  <head>
    <title>KK Time Database Setup</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
  </head>

  <body>

    <?php
    // Website Header
    require "header.php"

    ?>

    <section id="login_section">
      <div class="narrowcontainer">
        <div class="stdbox">
          <h1>KK Time Database Setup</h1>
          <p>
            Willkommen im KK Time Database Setup. Bitte geben Sie in den folgenden
            Feldern die Zugangsdaten zur My-SQL Datenbank an. Das Setup wird danach
            automatisch die benötigte Tabellenstruktur sowie den Standardbenutzer
            <i>admin</i> mit dem Password <i>admin</i> anlegen.
          </p>
          <form action="actions/setup.act.php" method="post">
            <p>
              My-SQL Hostserver: (localhost falls der My-SQL Server auf dem Websiteserver läuft)<br />
              <input type="text" name="setup-sqlhostname" placeholder="My-SQL Datenbank Hostname"/>
            </p>
            <p>
              My-SQL Nutzername:<br />
              <input type="text" name="setup-sqlusername" placeholder="My-SQL Datenbank Nutzername"/>
            </p>
            <p>
              My-SQL Passwort:<br />
              <input type="text" name="setup-sqlpassword" placeholder="My-SQL Datenbank Passwort"/>
            </p>
            <p>
              My-SQL Datenbank Name:<br />
              <input type="text" name="setup-sqldbname" placeholder="My-SQL Datenbank Name"/>
            </p>
            <button type="submit" name="setup-submit" style="margin: 10px 0px;">Fortfahren</button>
          </form>
          <p style="color: red;">
            <b>Warnung:</b> Die Datenbank, welche angegeben wird, wird im Zuge des Setups <b>VOLLSTÄNDIG GELÖSCHT</b>.
            Dadurch gehen alle Daten in der angegebenen Datenbank verloren.
          </p>
        </div>
      </div>
    </section>

    <?php

    require "footer.php";

    ?>

  </body>
</html>
