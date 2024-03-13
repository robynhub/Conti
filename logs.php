<?php 
/**
 ****************************************************************************
 *    Copyright (C) 2004 by Antonio Bartolini                               *
 *    bartolin@cli.di.unipi.it                                              *
 *                                                                          *
 *    This program is free software; you can redistribute it and*or modify  *
 *    it under the terms of the GNU General Public License as published by  *
 *    the Free Software Foundation; either version 2 of the License, or     *
 *    (at your option) any later version.                                   *
 ****************************************************************************
 */
include "libs/db.inc.php";
include "libs/draw.inc.php";
include "libs/config.inc.php";

/** Controllo accessi */
if (!($_COOKIE['user'] && $_COOKIE['pass'])) {
  header("Location: index.php");
} elseif (! query_utente($_COOKIE['user'],$_COOKIE['pass'])){
  header("Location: index.php");
  header("Pragma: no-cache");
}
$user = $_COOKIE['user']; $password = $_COOKIE['pass'];
/** Fine controllo accessi */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<?php DrawHeader(true); ?>
<body>
<?php DrawMenu($user);
?>

<table style="text-align: left; width: 100%;" border="0"
cellpadding="2" cellspacing="2">
<tbody>
<tr>
<td style="width: 54px;"></td>
<td style="width: 1093px;"></td>
<td style="width: 54px;"></td>
</tr>
<tr>
<td style="width: 54px;"></td>
<td border="0" bgcolor='#FFFFFF'
style="width: 1093px; font-family: monospace; color: rgb(255, 255, 225); height: 100px; vertical-align: top; "><fieldset><span
style="color: rgb(0, 0, 0); font-family: Courier New,Courier,monospace;"><ul style='border:0px solid #a0a0a0;padding-bottom:2px;padding-right:2px'>
<?php
print "<h2>Log:</h2>";
read_log(100);
?>

