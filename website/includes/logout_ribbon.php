<section id="logout_section">
  <div class="stdcontainer">
    <ul>
      <li>
        <form action="actions/logout.act.php" method="post">
          <button type="submit" name="logout-submit" id="logoutbutton">Logout</button>
        </form>
      </li>
      <li>
        <p>
          Eingeloggt als:<b>
          <?php
          echo " ".$_SESSION["username"];
          ?>
          </b>
        </p>
      </li>
    </ul>
  </div>
</section>
