<?php

function generate_checkbox($checkbox_id, $checkbox_string)
{
  echo "<label class=\"ccb_container\">".$checkbox_string."<input type=\"checkbox\" name=\"".$checkbox_id."\"/><span class=\"ccb_checkmark\"></span></label>";
}

?>
