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
include "libs/tornaconti.inc.php";


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
?>
</b><div class="container" align="center">
	
	<?php
	print "<br><h2>Calcolo Tornaconti</h2>\n";
	print "<div class='row' align='center'>\n";
	
		$apply = false;
		if (isset($_POST['Applica']) && $_POST['Applica'] && $_POST['Applica'] == "OK"){
		  logga("=== $user Applica Tornaconto: ===");
		  $apply = true;
		}
		
		// the real work! 
		// prendo gli utenti
		$utenti = Get_Utenti();
		// mi faccio una tabella dei debiti e la disegno
		print "\n<div class='col-xs-6 col-md-6' align='left'>\n";
			$output =  Get_Tabella($utenti,$apply);
			draw_tornaconto($output['1'],"situazione.jpg");
		print "</div><div class='col-xs-6 col-md-6' align='center'>";
		
			// applico il tornaconto: primo passo...
			$tornaconto = Tornaconto_1st_pass($output['0'],$utenti);
			// ...ed ecco secondo passo!
			$tornaconto = Tornaconto_2nd_pass($tornaconto,$utenti,$apply);
			// controllo le differenze con il db attuale
			$risultati  = SQL_Applica_Modifiche($tornaconto);
			print "</div><div class='col-xs-6 col-md-6' align='left'>";
			
			if (count($risultati) != 0){
			  //print "<br>&nbsp;<table style=\"text-align: left; width: 100%; \" border=\"0\" cellpadding=\"1\" cellspacing=\"1\"><tbody><tr><td>"; 
			  print "<h2>Tornaconto:</h2>";
			  //print "</td></tr></tbody></table><br>"; 
			  if ($apply) logga("=== Tornaconto: ===");
			  foreach($tornaconto as $value){
			    if ( $value['0'] != 0 ){
			      print "$value[1] deve a $value[2] ";
			      printf("%.2f",$value['0']);
			      print " euro.<br>";
			      if ($apply){ 
				$simp = number_format($value['0'],2);
				logga("$value[1] deve a $value[2]: $simp euro.");
			      }
			    }
			  }
			  draw_tornaconto($tornaconto,"tornaconto.jpg");
		  print "</div>\n";
		print "</div>\n";
	  
	  if ($apply) 
	    logga("=== Query Necessarie: ===");
	  print "<hr><div class='container' align='left'> ";
	  print "<h3>Query Necessarie:</h3>\n";
	  foreach($risultati as $value){
	    print "$value<br>\n";
	    if ($apply) logga("$value");
	  }
	  
	  print "</div><br>";
	  if ($apply) {
	    print "<h3>Modifiche Applicate con Successo!<h3>";
	    logga("=== Tornaconto Applicato da $user ===");
	    
	    foreach($risultati as $query){
	      $link = mysqli_connect($dbhost,$dbuser,$dbpass);
	      @mysqli_select_db($link,$dbname) or die('Could not select database a');
	      mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
	      mysqli_close($link);
	    }
	  }
	  else
	    print "<form  method=\"post\" action=\"tornaconti.php\" name=\"Apply\"><button class='btn btn-primary btn-lg' value=\"OK\" name=\"Applica\">Applica</button></form>";
	  
	  
	} else {
	  print "<br><h3>Tornaconto Non Possibile.</h3><br>&nbsp";
	}
	?>
	</ul></span></big></big>
	</td>
	<td style="width: 54px;"></td>
	</tr>
	<tr>
	<td style="width: 54px;"></td>
	<td style="width: 1093px;"></td>
	<td style="width: 54px;"></td>
	</tr>
	</tbody>
	</table>
</div>	
	
</body>
</html>
