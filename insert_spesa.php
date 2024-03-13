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
<body >
<?php
DrawMenu($user);
print "<br>";
$scadenza = $_GET['scad_anno'] . "-" . $_GET['scad_mese'] . "-" . $_GET['scad_giorno'];
if ($_GET['Causale'] && $_GET['Importo'] && $_GET['num_Utente']){
	if ($_GET['Causale'] == "qualsiasi" && ! $_GET['newcausale']) die("Errore nell' immissione dei campi");
	if ($_GET['Causale'] == "qualsiasi" && $_GET['newcausale']) $Causale = $_GET['newcausale'];
	else $Causale = $_GET['Causale'];
	
	 $utenti= array();
	 for ($i = 0 ; $i < $_GET['num_Utente'] ; $i++){
	   $index = "Utente$i"; 
	   if ( $_GET[$index] )
	     array_push($utenti,$_GET[$index]);  
	 }
	
	Insert_Spesa($_GET['Descrizione'],$Causale,$scadenza, $_GET['Importo'], $user, $utenti);
	PrintLineTable("Spesa Inserita Correttamente");
} else {

?>

<div class="container">
<h1>Inserisci Bolletta</h1>
<div class="col-xs-6 col-md-6" align="left">
<form method="get" action="insert_spesa.php" name="Spesa">
<div class="form-group row">
  <label for="example-text-input" class="col-sm-2 col-form-label col-form-label-sm">Descrizione:</label>
  <div class="col-xs-10">
	<input class="form-control" type="text" maxlength="30"  name="Descrizione" placeholder="Descrizione">
  </div>
</div>
<div class="form-group row">
  <label for="example-text-input" class="col-sm-2 col-form-label col-form-label-sm">Causale:</label>
  <div class="col-xs-10">
		<?php form_list("Spese","Causale","Causale","Aggiungi una nuova Causale"); ?>&nbsp;<img src="arrow.gif">
        <input  class="form-control" type="text" name="newcausale" placeholder="Nuova Causale">
  </div>
</div>
<div class="form-group row">
  <label for="example-text-input" class="col-sm-2 col-form-label col-form-label-sm">Scadenza:</label>
  <div class="col-xs-10">
  	<?php form_data("scad_giorno","scad_mese","scad_anno"); ?>
  </div>
</div>
<div class="form-group row">
  <label for="example-text-input" class="col-sm-2 col-form-label col-form-label-sm">Importo:</label>
  <div class="col-xs-10">
  	<input class="form-control" type="text" name="Importo" placeholder="0.00">
  </div>
</div>
<div class="form-group row">
  <label for="example-text-input" class="col-sm-2 col-form-label col-form-label-sm">Dividi Con:</label>
  <div class="col-xs-10">
  <?php radio_list("Utenti","Utente","Username",$_COOKIE['user']);  ?>
  </div>
</div>
<div class="form-group row">
	<button class="btn btn-primary btn-lg" name="Invia" value="Invia">Invia</button>
</div>
</div>
</form>
</div>
<?php
}
?>


</body>
</html>
