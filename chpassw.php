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
<?php DrawHeader(); ?>
<body>
<?php DrawMenu($user); 
if ($_POST['oldpassword'] && $_POST['newpassword'] && $_POST['newpassword2'] && $_POST['newpassword'] == $_POST['newpassword2'] && Cambia_Password($user,$_POST['oldpassword'],$_POST['newpassword'])) PrintLineTable("Password Cambiata!");
else {
?>
<div class="container">
	<h1>Cambia Password</h1>
	<form method="post" action="chpassw.php" name="chpassw">
	<div class="form-group row">
	  <label for="oldpwd" class="col-sm-2 col-form-label col-form-label-sm" >Vecchia Password:</label>
	  <div class="col-xs-10">
		<input size="30" maxlength="30" name="oldpassword" type="password">  
	  </div>
	</div>
	<div class="form-group row">
	  <label for="newpwd" class="col-sm-2 col-form-label col-form-label-sm">Nuova Password:</label>
	  <div class="col-xs-10">
		<input maxlength="30" size="30" name="newpassword" type="password">
	   </div>
	</div>
	<div class="form-group row">
	  <label for="newpwd2" class="col-sm-2 col-form-label col-form-label-sm">Ripeti Nuova Password:</label>
	  <div class="col-xs-10">
		<input maxlength="30"  size="30" name="newpassword2" type="password">
	   </div>
	</div>
	<div class="form-group row">
		<button class="btn btn-primary btn-lg" name="Invia" value="Invia">Invia</button>
	</div>
	</form>
</div>
<?php
}
?>
</body>
</html>
