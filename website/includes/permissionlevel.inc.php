<?php

//Parser for the permissionlevel of a user

$_PERMISSION_LEVELS =
[
  "user" => 0,
  "manager" => 1,
  "admin" => 10000,
];

$_PERMISSION_LEVEL_DEFAULT = 0;

function str_to_permissionlevel($perm_str)
{
  global $_PERMISSION_LEVELS;
  if (isset($_PERMISSION_LEVELS[$perm_str]))
  {
    return $_PERMISSION_LEVELS[$perm_str];
  }
  else
  {
    return -1;
  }
}

function permissionlevel_to_str($perm_lvl)
{
  global $_PERMISSION_LEVELS;
  global $_PERMISSION_LEVEL_DEFAULT;
  foreach (array_keys($_PERMISSION_LEVELS) as $perm_str)
  {
    if ($_PERMISSION_LEVELS[$perm_str] === $perm_lvl)
    {
      return $perm_str;
    }
  }
  return permissionlevel_to_str($_PERMISSION_LEVEL_DEFAULT);
}

?>
