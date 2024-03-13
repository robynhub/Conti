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
<?php
DrawMenu($user);
print "<br>";
if ($_GET['Descrizione'] && $_GET['Importo'] && $_GET['num_Utente']){
	 $utenti= array();
	 for ($i = 0 ; $i < $_GET['num_Utente'] ; $i++){
	   $index = "Utente$i"; 
	   if ( $_GET[$index] )
	     array_push($utenti,$_GET[$index]);  
	 }
	 if (sizeof($utenti) == 0) { 
		 die("Selezionare almeno un destinatario"); 
	 }

	Insert_Pagamento($_GET['Descrizione'], $_GET['Importo'], $user, $utenti);
	PrintLineTable("Pagamento Inserito Correttamente.<br>Si Consiglia di applicare il <a href='tornaconti.php'>Tornaconto</a>.");

} else {

?>

<div class="container">
<h1>Inserisci Pagamento</h1>
<div class="col-xs-6 col-md-6" align="left">
<form method="get" action="insert_pagamento.php" name="Pagamento">
<div class="form-group row">
  <label for="descrizione" class="col-sm-2 col-form-label col-form-label-sm">Descrizione:</label>
  <div class="col-xs-10">
	<input class="form-control" type="text" maxlength="30" name="Descrizione" placeholder="Descrizione">
  </div>
</div>
<div class="form-group row">
  <label for="importo" class="col-sm-2 col-form-label col-form-label-sm">Importo:</label>
  <div class="col-xs-10">
  <input  class="form-control" type="text" maxlength="30" name="Importo" placeholder="0.00">
  </div>
</div>
<div class="form-group row">
  <label for="dividicon" class="col-sm-2 col-form-label col-form-label-sm">Dividi con:</label>
  <div class="col-xs-10">
  <?php radio_list("Utenti","Utente","Username",$_COOKIE['user']);  ?>
  </div>
</div>
<div class="form-group row">
	<button class="btn btn-primary btn-lg" name="Invia" value="Invia">Invia</button>
</div>

</form>
</div>
</div>
</form>
<?php
}
?>


</body>
</html>
