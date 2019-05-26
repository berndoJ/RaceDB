<?php

include __DIR__."/includes/phpheader.inc.php";

if (isset($_SESSION["username"]))
{
  header("Location: ./index.php");
  exit();
}

?>

<!DOCTYPE html>

<html>

  <head>
    <title>KK Time Database Login</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/login.css" />
    <link rel="shortcut icon" type="image/x-icon" href="favicon.png"/>
  </head>

  <body>

    <?php
    // Website Header
    include __DIR__."/header.php";

    ?>

    <section id="login_section">
      <div class="narrowcontainer">

        <?php

        if (isset($_GET["info"]))
        {
          ?>
            <div class="infobox">
              <h3>Logout Erfolgreich</h3>
              <p>
                <?php
                  switch ($_GET["info"])
                  {
                    case "logged_out":
                      echo "Sie wurden erfolgreich ausgeloggt.";
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
                      echo "Es wurden nicht alle Felder ausgefüllt.";
                      break;
                    case "bad_sql":
                      echo "Ein Datenbank-Fehler (SQL-Fehler) ist aufgetreten. Bitte
                        kontaktieren Sie den Websitebetreiber und informieren Sie ihn über diese Fehlermeldung.";
                      break;
                    case "invalid_password":
                      echo "Das angegebene Passwort ist nicht korrekt.";
                      break;
                    case "invalid_username":
                      echo "Der angegebene Benutzername wurde nicht gefunden.";
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
          <form action="actions/login.act.php" method="post">
            <h1>KK Time Database Login</h1>
            <p>
              Benutzername <br/>
              <input type="text" name="login-username" placeholder="Benutzername"/>
            </p>
            <p>
              Passwort <br/>
              <input type="password" name="login-password" placeholder="Passwort"/>
            </p>
            <button type="submit" name="login-submit" style="margin: 10px 0px;">Login</button>
          </form>
        </div>
      </div>
    </section>

    <?php

    include __DIR__."/footer.php";

    ?>

  </body>
</html>
