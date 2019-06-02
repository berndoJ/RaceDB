<?php

include __DIR__ . "/includes/phpheader.inc.php";

if (isset($_SESSION["username"])) {
  header("Location: ./index.php");
  exit();
}

?>

<!DOCTYPE html>

<html>

<head>
  <title>RaceDB - Login</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="./css/defstyle.css" />
  <link rel="stylesheet" href="./css/login.css" />
  <link rel="shortcut icon" type="image/x-icon" href="favicon.png" />
  <script type="application/javascript" src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script type="application/javascript" src="./js/std_server_responses.js"></script>
  <script type="application/javascript" src="./js/std_notif_messages.js"></script>
</head>

<body>

  <?php
  // Website Header
  include __DIR__ . "/header.php";

  // Notification Lib
  include __DIR__ . "/notifications.php";

  ?>

  <section id="login_section">
    <div class="narrowcontainer">

      <?php

      if (isset($_GET["info"])) {
        ?>
        <div class="infobox">
          <h3>Logout Erfolgreich</h3>
          <p>
            <?php
            switch ($_GET["info"]) {
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

      if (isset($_GET["error"])) {
        ?>
        <div class="errorbox">
          <h3>Fehler</h3>
          <p>
            <?php
            switch ($_GET["error"]) {
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
          <h1>RaceDB Login</h1>
          <p>
            Benutzername <br />
            <input type="text" name="login-username" placeholder="Benutzername" />
          </p>
          <p>
            Passwort <br />
            <input type="password" name="login-password" placeholder="Passwort" />
          </p>
          <button type="submit" name="login-submit" style="margin: 10px 0px;">Login</button>
        </form>
      </div>

      <div class="stdbox">
        <h1>Zeitabfrage</h1>
        <?php
        require_once __DIR__ . "/includes/utils.inc.php";
        $active_run = db_get_active_run();
        if ($active_run == null) {
          ?>
          <p><i>Momentan ist kein Lauf aktiv.</i></p>
        <?php
      } else {
        ?>
          <p>
            <b>Läufer-UID</b> <i>(Zum Absenden [Enter] drücken)</i><br />
            <input type="text" class="acquisition_input" id="time_query_input" placeholder="Läufer-UID" />
            <script type="application/javascript">
              $(window).on("load", function() {
                $("#time_query_input").keydown(function(e) {
                  if (e.keyCode == 13) {
                    query_time();
                    $("#time_query_input").val("");
                  }
                });
              });
            </script>
          </p>

          <p>
            <span style="font-size: 40px;">
              <span id="time_query_ctr_maj">--:--:--</span><span id="time_query_ctr_min" style="font-size: 18px;">.---</span>
            </span>
          </p>
          <script type="application/javascript">
            function query_time() {
              ruid = $("#time_query_input").val().trim();
              if (ruid == "") {
                return;
              }

              $.get("actions/get_runner_time.act.php", {
                runid: <?php echo $active_run; ?>,
                runneruid: ruid
              }, function(data) {
                data_sec = data.split("\n");
                if (data_sec[0] != RESPONSE_SUCCESS) {
                  display_notification("info", "Diesem Läufer ist noch keine Zeit zugeteilt. Entweder ist die UID falsch geschrieben oder der Läufer wurde noch nicht ins System aufgenommen.");
                  return;
                }
                console.log(data_sec[1]);
                maj = __ultc_ms_str(data_sec[1], true);
                min = __ultc_ms_str(data_sec[1], false);
                $("#time_query_ctr_maj").html(maj);
                $("#time_query_ctr_min").html("." + min);
                display_notification("success", "Die Laufzeit wurde erfolgreich ermittelt.");
              });
            }

            function __ultc_ms_str(s, t) {
              // Pad to 2 or 3 digits, default is 2
              function pad(n, z) {
                z = z || 2;
                return ('00' + n).slice(-z);
              }

              var ms = s % 1000;
              s = (s - ms) / 1000;
              var secs = s % 60;
              s = (s - secs) / 60;
              var mins = s % 60;
              var hrs = (s - mins) / 60;

              if (t == true) {
                return pad(hrs) + ':' + pad(mins) + ':' + pad(secs);
              } else {
                return pad(ms, 3);
              }
            }
          </script>
        <?php
      }
      ?>
      </div>
    </div>
  </section>

  <?php

  include __DIR__ . "/footer.php";

  ?>

</body>

</html>