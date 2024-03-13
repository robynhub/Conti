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
<head>

	<?php DrawHeader(true); ?>
</head>
<body>
<?php DrawMenu($user); ?>


<div class="container" align="center">
	<h1> Ricerca </h1>
	<div class="row" align="left">
		<div class="col-xs-6 col-md-6" align="left">
			<form method="get" action="search.php" name="RicercaSpesa">
				<div class="form-group row">
				  <label for="IDSpesa" class="col-sm-2 col-form-label col-form-label-sm">IDSpesa</label>
				  <div class="col-xs-10">
				    <input class="form-control form-control-sm" type="text" placeholder="IDSpesa" name="idspesa">
				  </div>
				</div>
				<div class="form-group row">
				  <label for="Utente" class="col-sm-2 col-form-label col-form-label-sm">Utente</label>
				  <div class="col-xs-10">
				    <?php form_list("Utenti","Username","utente"); ?>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="Causale" class="col-sm-2 col-form-label col-form-label-sm">Causale</label>
				  <div class="col-xs-10">
				    <?php form_list("Spese","Causale","causale"); ?>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="Pagato" class="col-sm-2 col-form-label col-form-label-sm">Pagato</label>
				  <div class="col-xs-10">
				    <select name="pagato">
						<option selected="selected" value="qualsiasi">Qualsiasi</option>
						<option value="si">Si</option>
						<option value="no">No</option>
					</select>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="limit" class="col-sm-2 col-form-label col-form-label-sm">Limite</label>
				  <div class="col-xs-10">
				    	<select name="limit">
							<option selected="selected" value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>
				  </div>
				</div>	
				<div class="form-group row">
				  <label for="orderby" class="col-sm-2 col-form-label col-form-label-sm">Ordina per Scadenza</label>
				  <div class="col-xs-10">
				    	<input checked="checked" value="si" name="orderbydata" type="checkbox">
				  </div>
				</div>
				<div class="form-group row">
				  <div class="col-xs-10">
					    <input type="hidden" name="type" value="spesa">
				    	<button class="btn btn-primary" name="Ricerca">Ricerca</button>
				  </div>
				</div>
			</form>
		</div>
		<div class="col-xs-6 col-md-6" align="left">
			<form method="get" action="search.php" name="RicercaMovimento">
				<div class="form-group row">
				  <label for="IDMovimento" class="col-sm-2 col-form-label col-form-label-sm">IDMovimento</label>
				  <div class="col-xs-10">
				    <input class="form-control" type="text" placeholder="IDMovimento" name="idmovimento">
				  </div>
				</div>
				<div class="form-group row">
				  <label for="IDSpesa" class="col-sm-2 col-form-label col-form-label-sm">IDSpesa</label>
				  <div class="col-xs-10">
				    <input class="form-control" type="text" placeholder="IDSpesa" name="idspesa">
				  </div>
				</div>	
				<div class="form-group row">
				  <label for="Utente" class="col-sm-2 col-form-label col-form-label-sm">Utente</label>
				  <div class="col-xs-10">
				    <?php form_list("Utenti","Username","utente"); ?>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="Causale" class="col-sm-2 col-form-label col-form-label-sm">Causale</label>
				  <div class="col-xs-10">
				    <?php form_list("Movimenti","Causale","causale"); ?>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="Pagato" class="col-sm-2 col-form-label col-form-label-sm">Pagato</label>
				  <div class="col-xs-10">
				    <select name="pagato">
						<option selected="selected" value="qualsiasi">Qualsiasi</option>
						<option value="si">Si</option>
						<option value="no">No</option>
					</select>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="limit" class="col-sm-2 col-form-label col-form-label-sm">Limite</label>
				  <div class="col-xs-10">
				    	<select id="limit" name="limit">
							<option selected="selected" value="10">10</option>
							<option value="50">50</option>
							<option value="100">100</option>
						</select>
				  </div>
				</div>
				<div class="form-group row">
				  <label for="orderby" class="col-sm-2 col-form-label col-form-label-sm">Ordina per ID</label>
				  <div class="col-xs-10">
				    	<input checked="checked" value="si" name="orderbydata" type="checkbox">
				  </div>
				</div>
				<div class="form-group row">
				  <div class="col-xs-10">
					    <input type="hidden" name="type" value="movimento">
				    	<button class="btn btn-primary" name="Ricerca">Ricerca</button>
				  </div>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="container">
<hr style="width: 100%; height: 2px;"> 
<?php
if ($_GET['type'] == "spesa"){
  Search_Spesa($_GET['idspesa'],$_GET['utente'],$_GET['causale'],$_GET['pagato'],$_GET['limit'],$_GET['orderbydata'],$user);
} elseif ($_GET['type'] == "movimento"){
  Search_Movimenti($_GET['idmovimento'],$_GET['idspesa'],$_GET['utente'],$_GET['causale'],$_GET['pagato'],$_GET['limit'],$_GET['orderbydata'],$user);
}
?>
</div>
</body>
</html>
