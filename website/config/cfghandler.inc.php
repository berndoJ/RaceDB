<?php

class Config
{
  protected $cfg_array;
  protected $cfg_filename;

  // Loads the configuration
  public function load($filename)
  {
    $this->cfg_filename = $filename;
    $this->cfg_array = require $this->cfg_filename;
  }

  // Reloads the configuration
  public function reload()
  {
    $this->cfg_array = require $this->cfg_filename;
  }

  // Gets a configuration setting
  public function get($cfg_key, $default_return = null)
  {
    $array_segments = explode(".", $cfg_key);

    $current_segment = $this->cfg_array;

    foreach ($array_segments as $array_segment)
    {
      if (isset($current_segment[$array_segment]))
      {
        $current_segment = $current_segment[$array_segment];
      }
      else
      {
        return $default_return;
      }
    }

    return $current_segment;
  }

  // Sets a config value.
  public function set($cfg_key, $value)
  {
    $array_segments = explode(".", $cfg_key);

    $current_segment = &$this->cfg_array;

    foreach ($array_segments as $array_segment)
    {
      $current_segment = &$current_segment[$array_segment];
    }

    $current_segment = $value;
  }

  // Saves the configuration.
  public function save()
  {
    $cfg_string = "<?php\nreturn ".var_export($this->cfg_array, true).";\n?>";
    $cfg_file = fopen($this->cfg_filename, "w+");
    fwrite($cfg_file, $cfg_string);
    fclose($cfg_file);
  }
}

function retrieve_default_config()
{
  $cfg = new Config();
  $cfg->load(__DIR__."/config.php");
  return $cfg;
}

?>
