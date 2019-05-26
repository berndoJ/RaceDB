<?php

include __DIR__."/includes/phpheader.inc.php";
include __DIR__."/includes/requireadmin.inc.php";

?>

<!DOCTYPE html>

<html>

  <head>
    <title>KK Time Database - Neuen Benutzer Erstellen</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
  </head>

  <body>

    <?php
    // Website Header
    include __DIR__."/header.php"

    ?>

    <section id="create_new_user_section">
      <div class="stdcontainer">

        <?php
        if (isset($_GET["error"]))
        {
        ?>
        <div class="errorbox">
          <h3>Fehler</h3>
          <p>
            <?php
            switch ($_GET["error"])
            {
              case "empty_fields":
                echo "Es wurden nicht alle benötigten Felder ausgefüllt.";
                break;
              case "password_match_failed":
                echo "Die eingegeben Passwörter sind nicht identisch.";
                break;
              case "bad_sql":
                echo "Ein Datenbank-Fehler (SQL-Fehler) ist aufgetreten. Bitte
                  kontaktieren Sie den Websitebetreiber und informieren Sie ihn über diese Fehlermeldung.";
                break;
              case "username_taken":
                echo "Der angegebene Benutzername existiert bereits.";
                break;
              case "invalid_permission_level":
                echo "Die Angegebenen Benutzerrechte sind fehlerhaft.";
                break;
              default:
                echo "Unbekannter Fehler.";
                break;
            }
            ?>
          </p>
        </div>
        <?php
        }
        ?>

        <div class="stdbox">
          <form action="actions/createuser.act.php" method="post">
            <h1>Neuen Benutzer Erstellen</h1>
            <p>
              Benutzername<br/>
              <input type="text" name="createuser-username" placeholder="Benutzername"/>
            </p>
            <p>
              Passwort<br/>
              <input type="password" name="createuser-password" placeholder="Passwort"/>
            </p>
            <p>
              Passwort erneut eingeben<br/>
              <input type="password" name="createuser-password-confirm" placeholder="Passwort erneut eingeben"/>
            </p>
            <p>
              Benutzerrechte<br/>
              <select type="select" name="createuser-permissionlevel">
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
            <ul class="horizontal_ul">
              <li>
                <a class="buttonlink" href="./settings.php">Abbrechen</a>
              </li>
              <li>
                <button type="submit" name="createuser-submit" style="margin: 10px 10px;">Erstellen</button>
              </li>
            </ul>
          </form>
        </div>

      </div>
    </section>

    <?php

    include __DIR__."/footer.php";

    ?>

  </body>
</html>
