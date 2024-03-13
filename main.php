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
print "<div class='container' align='center'>";
PrintLineTable("Bollette da Pagare");
Query_Spese_Da_Pagare($user);
PrintLineTable("Debiti da Saldare");
Query_Movimenti_Da_Saldare($user);
?>
<hr>


	<div class="row" align="center">
		<div class="col-xs-6 col-md-6" align="center">
			
			<h3>Totale Debiti</h3>
			<?php
			//PrintLineTable("Totale Debiti");
			Query_Saldi($user);
			?>

		</div>
		<div class="col-xs-6 col-md-6" align="center">

			<h3>Totale Crediti</h3>
			<?php
			//PrintLineTable("Totale Crediti");
			Query_Crediti($user);
			?>

		</div>
	</div>
</div>
<hr>
<?php
PrintLineTable("Movimenti non Saldati");
Query_Movimenti_Non_Saldati($user);
?>
</body>
</html>
