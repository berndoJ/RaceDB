<?php

include __DIR__."/includes/phpheader.inc.php";
include __DIR__."/includes/requireadmin.inc.php";

?>

<!DOCTYPE html>

<html>

  <head>
    <title>KK Time Database - Einstellungen</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
    <link rel="stylesheet" href="./css/no_link_style.css" />
  </head>

  <body>

    <?php
    // Website Header
    include __DIR__."/header.php";

    include __DIR__."/includes/logout_ribbon.php";

    ?>

    <section id="settings_section">

      <!-- Displayed information container -->
      <div class="stdcontainer">
        <?php
        if (isset($_GET["info"]))
        {
        ?>
        <div class="successbox">
          <h3>Information</h3>
          <p>
          <?php
          switch ($_GET["info"])
          {
            case "create_user_success":
              echo "Der Benutzer ".$_GET["user"]." wurde erfolgreich erstellt!";
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

        <div class="stdbox">
          <p>
            Hier können einige Administratoreinstellungen vorgenommen werden.
          </p>
        </div>

        <a href="./create_user.php">
          <div class="selectionbox">
            <p>
              <b>Neuen Benutzer erstellen</b>
            </p>
          </div>
        </a>

        <a href="./manage_users.php">
          <div class="selectionbox">
            <p>
              <b>Benutzermanagement</b>
            </p>
          </div>
        </a>

      </div>
    </section>

    <?php

    include __DIR__."/footer.php";

    ?>

  </body>
</html>
