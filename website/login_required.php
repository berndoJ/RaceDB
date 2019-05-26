<?php

include __DIR__."/includes/phpheader.inc.php";

?>

<!DOCTYPE html>

<html>

  <head>
    <title>KK Time Database - Login Required</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="./css/defstyle.css" />
  </head>

  <body>

    <?php
    // Website Header
    include __DIR__."/header.php";

    ?>

    <section id="login_section">
      <div class="narrowcontainer">
        <div class="stdbox">
          <h2>Login Erforderlich</h2>
          <p>
            Um diese Seite einzusehen, ist eine Anmeldung notwendig.
          </p>
          <form action="./login.php">
            <button type="submit">Zur Login-Seite</button>
          </form>
        </div>
      </div>
    </section>

    <?php

    include __DIR__."/footer.php";

    ?>

  </body>
</html>
