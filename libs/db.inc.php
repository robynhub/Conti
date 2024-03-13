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
 * Libreria per l' interfacciamento con il database MySQL 
 *
 * function Query_Spese_Da_Pagare($user)
 * function Query_Movimenti_Da_Saldare($user)
 * function Query_Saldi($user)
 * function Query_Movimenti_Non_Saldati($user)
 * function Query_World_Debit()
 * function Query_World_Credit($user)
 * function Insert_Spesa($Descrizione, $Causale, $Scadenza, $Importo,$user,$da_dividere)
 * function Paga_Movimento($id_movimento,$importo,$oldimporto)
 * function Paga_Spesa($id_spesa)
 * function Query_Movimento($IDMovimento)
 * function Query_Utente($user,$md5password)
 * function Insert_Utente($user,$password)
 * function form_list($table,$field,$name,$any="Qualsiasi")
 * function form_data($fieldg,$fieldm,$fielda)
 * function Search_Movimenti($idmovimento,$idspesa,$utente,$causale,$pagato,$limit,$ordina,$user)
 * function Search_Spesa($idspesa,$utente,$causale,$pagato,$limit,$ordina,$user)
 * function Cambia_Password($user,$oldpassword,$newpassword)
 *
 */

error_reporting(0);

function mysqli_result($res,$row=0,$col=0){
    $numrows = mysqli_num_rows($res);
    if ($numrows && $row <= ($numrows-1) && $row >=0){
        mysqli_data_seek($res,$row);
        $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
        if (isset($resrow[$col])){
            return $resrow[$col];
        }
    }
    return false;
}

function logga($string){
  include 'config.inc.php';
  $file = fopen($logfile,'a');
  exec("date +\"[%d/%m/%y %X]\"",$date,$ret);
  $string =  $date['0'] . " " . $string . "\n";
  fwrite($file, $string);
  fclose($file);
}

function read_log($lines=null){
  include 'config.inc.php';
  if ($lines)
    exec("tail -$lines $logfile",$lines,$return);
  else
    exec("tail $logfile",$lines,$return);
  
  foreach($lines as $line){
    print $line . "<br>";
  }
  
}



/** Restituisce una tabella bidimensionale contenente
    tutti le spese non pagate mostrando il campo pagato
    nel caso in cui la spesa e' stata inserita da $user     */

function Query_Spese_Da_Pagare($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select * from Spese where Pagato = 'no' order by IDSpesa;";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $result_cols = mysqli_query($link,"SHOW COLUMNS FROM Spese;");
  for($i=0;$i<mysqli_num_fields($result);$i++)
    {$cols[] = mysqli_result($result_cols, $i);
    }
  // Printing results in HTML
  print "<div class='container'><fieldset>\n";
  print "<table class=\"table\"><thead class=\"thead-inverse\">";
  $line = mysqli_fetch_array($result_cols, MYSQLI_ASSOC);
  foreach ($cols as $col_value) {
    echo "\t\t<th><b>$col_value</b></th>\n";
  }
  echo "<th></th></thead>";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    list($scad_a,$scad_m,$scad_g) = explode("-",$line['Scadenza'],3);
    if (mktime(0,0,0,$scad_m,$scad_g,$scad_a) <= mktime(0,0,0,date("m"),date("d"),date("Y")) && $line['Scadenza'] != null && $line['Pagato'] != "si") $color = "#FF0000";
    else $color = "#000000";
    echo "\t<tr >\n";
    foreach ($line as $col_value) {
      echo "\t\t<td><font size='2'> ";
      if ($color == "#FF0000") echo "<b>";
      if ($col_value == $line['IDSpesa']){
	//echo "<font size='2' color='$color' > ";
	echo "<a href='search.php?type=movimento&idspesa=$col_value'>$col_value</a>";
	echo "</font>\n"; } else { 
	  echo "\t\t$col_value\n";
	  if ($color == "#FF0000") echo "</b>";
	}
      echo "</td>";
    }
    echo "\t\t<td><font size='2'> ";
    if ($_COOKIE['user'] == $user && $line['Utente'] == $user) { 
      echo "<a href='paga.php?type=spesa&idspesa=$line[IDSpesa]'>Paga</a>";
    } 
    echo "</font></td>\n";
    
    echo "\t</tr>\n";
    
  }
  echo "</table>\n";
  echo "<br><b>Debito con il mondo: Euro  ";
  printf("%.2f",Query_World_Debit());
  echo "</b></fieldset></div>";	
  // Free resultset
  mysqli_free_result($result);
  
}




/** Restituisce una tabella bidimensionale contenente
    tutti i movimenti da saldare mostrando il campo pagato  */

function Query_Movimenti_Da_Saldare($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select * from Movimenti where Pagato = 'no' and Utente = '$user' order by IDMovimento desc;";
  
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $result_cols = mysqli_query($link,"SHOW COLUMNS FROM Movimenti;");
  for($i=0;$i<mysqli_num_fields($result);$i++)
    {$cols[] = mysqli_result($result_cols, $i);
    }
  // Printing results in HTML
  print "<div class='container'><fieldset>\n";
  print "<table class=\"table table-hover\"><thead>";
  $line = mysqli_fetch_array($result_cols, MYSQLI_ASSOC);
  foreach ($cols as $col_value) {
    echo "\t\t<th>$col_value</th>\n";
  }
  echo "<th></th></thead>";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "\t<tr >\n";
    foreach ($line as $col_value) {
      if ($col_value == $line['Importo']){
	echo "\t\t<td><font size='2'> ";
	printf("%.2f",$col_value);
	echo "</font></td>\n"; } elseif ($col_value == $line['IDSpesa']){
	  echo "\t\t<td><font size='2'> ";
	  echo "<a href='search.php?type=spesa&idspesa=$col_value'>$col_value</a>";
	  echo "</font></td>\n"; } else {
	    echo "\t\t<td><font size='2' > $col_value</font></td>\n"; }
    }
    echo "\t\t<td><font size='2' >";
    //if ($_COOKIE['user'] == $user && ($line['Utente'] == $user || $line['Creditore'] == $user)) {
    echo "<a href='paga.php?type=movimento&idmovimento=$line[IDMovimento]'>Paga</a>";
    //} 
    echo "</font></td>\n";
    echo "\t</tr>\n";
    
  }
  echo "</table>\n";
  echo "</fieldset></div>";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
  
}

/** Restituisce una tabella bidimensionale contenente
     tutti i debiti da $user a tutti gli altri e il relativo
     totale 							*/

function Query_Saldi($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select Creditore, sum(Importo) as Importo from Movimenti where Pagato = 'no' and Utente = '$user' group by Creditore;";
  
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $cols = array("Creditore","Debito"); 
  // Printing results in HTML
  print "<table class=\"table table-hover\"><thead class=\"thead-inverse\">";
  foreach ($cols as $col_value) {
    echo "\t\t<th>$col_value</th>\n";
  }
  echo "<thead>\n";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "\t<tr >\n";
    foreach ($line as $col_value) {
      if ($col_value == $line['Importo']) {
	echo "\t\t<td><font size='2' >";
	printf("%.2f",$col_value);
	echo "</font></td>\n"; 
      } else {
	echo "\t\t<td><font size='2' >$col_value</font></td>\n";
      }
    }
    echo "\t</tr>\n";
    
  }
  // Totale 
  $query = "select sum(Importo) as Importo from Movimenti where Pagato = 'no' and Utente = '$user';";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
  echo "<tr bgcolor='#FFFFFF'>";
  echo "<td ><b>Totale</b></td>";
  echo "<td ><b>";
  printf("%.2f",$line['Importo']);
  echo "</b></td>";
  echo "</table>\n";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
}

/** Restituisce una tabella bidimensionale contenente
     tutti i crediti da $user a tutti gli altri e il relativo
     totale                      */

function Query_Crediti($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select Utente, sum(Importo) as Importo from Movimenti where Pagato = 'no' and Creditore = '$user' group by Utente;";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $cols = array("Debitore","Credito"); 
  // Printing results in HTML
  print "<table class=\"table table-hover\"><thead class=\"thead-inverse\">";
  foreach ($cols as $col_value) {
    echo "\t\t<th>$col_value</th>\n";
  }
  echo "<thead>\n";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
      if ($col_value == $line['Importo']) {
	echo "\t\t<td>";
	printf("%.2f",$col_value);
	echo "</td>\n"; 
      } else {
	echo "\t\t<td>$col_value</td>\n";
      }
    }
    echo "\t</tr>\n";
    
  }
  // Totale 
  $query = "select sum(Importo) as Importo from Movimenti where Pagato = 'no' and Creditore = '$user';";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $line = mysqli_fetch_array($result, MYSQLI_ASSOC);
  echo "<tr bgcolor='#FFFFFF'>";
  echo "<td><b>Totale</b></td>";
  echo "<td><b>";
  printf("%.2f",$line['Importo']);
  echo "</b></td>";
  echo "</table>\n";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
}

/** Restituisce una tabella bidimensionale contenente 
      tutti i movimenti non saldati mostrando il campo pagato 
      nel caso in cui il movimento e' da o verso $user      */

function Query_Movimenti_Non_Saldati($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select * from Movimenti where Pagato = 'no' and Creditore = '$user' order by IDMovimento desc;";
  //  $query = "select IDMovimento, sum(Importo) as Importo, IDSpesa, Causale, Importo, Pagato  from Movimenti where Pagato = 'no' and Utente = '$user' group by IDSpesa;";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $result_cols = mysqli_query($link,"SHOW COLUMNS FROM Movimenti;");
  for($i=0;$i<mysqli_num_fields($result);$i++)
    {$cols[] = mysqli_result($result_cols, $i);
    }
  // Printing results in HTML
  print "<div class='container'><fieldset>\n";
  print "<table class=\"table table-hover\"><thead>";
  $line = mysqli_fetch_array($result_cols, MYSQLI_ASSOC);
  foreach ($cols as $col_value) {
    echo "\t\t<th>$col_value</td>\n";
  }
  echo "<th></th></thead>";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
      if ($col_value == $line['Importo']) { 
	// Diamo una visualizzazione decente all Importo
	echo "\t\t<td><font size='2'>";
	printf("%.2f",$col_value);
	echo "</font></td>\n"; 
      } elseif ($col_value == $line['IDSpesa']){
	echo "\t\t<td><font size='2'> ";
	echo "<a href='search.php?type=spesa&idspesa=$col_value'>$col_value</a>";
	echo "</font></td>\n"; } else {
	  echo "\t\t<td><font size='2' >$col_value</font></td>\n"; }
    }
    echo "\t\t<td><font size='2' >";
    echo "<a href='paga.php?type=movimento&idmovimento=$line[IDMovimento]'>Paga</a></font>";
    
    echo "</td>\n";
    echo "\t</tr>\n";
    
  }
  echo "</table>\n";
  echo "</fieldset></div>";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
  
}

/** Restituisce il debito del mondo (magari...) */
function Query_World_Debit(){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select sum(Importo) from Spese where Pagato = 'no';";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $line = mysqli_fetch_row($result); 
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
  
  return $line['0'];
  
}

/** Restituisce il Credito del mondo (magari...) */
function Query_World_Credit($user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "select sum(Importo) from Movimenti where Pagato = 'no' and Creditore = '$user';";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $line = mysqli_fetch_row($result); 
  // Free resultset
  mysqli_free_result($result);
  return $line['0'];
}

/** Inserisce un record nella tabella spese */
function Insert_Pagamento($Descrizione, $Importo,$user,$utenti){
	include 'config.inc.php';
	$data = date("Y-m-d");
	$Importo = strtr($Importo, ",", ".");
	$link = mysqli_connect($dbhost,$dbuser,$dbpass);
	@mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
	
	$num_utenti = count($utenti);
	// Divido l' importo fra tutti come dei Soci Capitalisti...
  	$Importo = ($Importo / $num_utenti );
  	
	foreach($utenti as $creditore){
	    $sql = "Insert into Movimenti (Utente,Creditore,IDSpesa,Causale,Importo,Pagato) VALUES ( '$creditore','$user', '0', '$Descrizione', '$Importo','no')";
	    mysqli_query($link,$sql) or die("Impossibile aggiungere il movimento per $creditore. " . $sql . " " . mysqli_error($link));
	    logga("$user: Creato Pagamento verso $creditore. Importo: $Importo" );
  	}
  	 mysqli_close($link); //e direi che era anche ora...
  	 return true;
}


/** Inserisce un record nella tabella spese */
function Insert_Spesa($Descrizione, $Causale, $Scadenza, $Importo,$user,$utenti){
  include 'config.inc.php';
  $data = date("Y-m-d");
  $Importo = strtr($Importo, ",", ".");
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "Insert into Spese (Descrizione,Causale,Scadenza,Importo,Utente,Data,Pagato) VALUES ('$Descrizione', '$Causale',  '$Scadenza','$Importo','$user','$data','no')";
  $result = mysqli_query($link,$sql) or die("Impossibile aggiungere la spesa. Contollare i campi. " . mysqli_error($link) );
  logga("$user: Inserita spesa $Descrizione. Tipo: $Causale, Scadenza: $Scadenza, Importo: $Importo");
  mysqli_close($link); 
  
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  
  //Vediamo di recuperare l' id spesa
  $sql = "select IDSpesa from Spese where Descrizione = '$Descrizione' and Causale = '$Causale' and Pagato = 'no';";
  $idspesaresult = mysqli_query($link,$sql) or die("Impossibile trovare l' IDSpesa Assegnato");
  $idspesa = mysqli_fetch_row($idspesaresult);
  $idspesa = $idspesa['0'];
  mysqli_free_result($idspesaresult);
  $num_utenti = count($utenti);
  // Divido l' importo fra tutti come dei bravi Comunisti...
  $Importo = ($Importo / ($num_utenti + 1));
  
  foreach($utenti as $debitore){
    $sql = "Insert into Movimenti (Utente,Creditore,IDSpesa,Causale,Importo,Pagato) VALUES ('$debitore', '$user', '$idspesa', '$Causale', '$Importo','no')";
    mysqli_query($link,$sql) or die("Impossibile aggiungere il movimento per $debitore.");
    logga("$user: Creato Movimento per $debitore. Tipo: $Causale, IDSpesa: $idspesa, Importo: $Importo, Pagato: no." );
  }
  
  mysqli_close($link); //e direi che era anche ora...
  return true;
}

/** Aggiorna un record nella tabella movimenti come pagato */
function Paga_Movimento($id_movimento,$importo,$oldimporto,$user="nouser"){
  include 'config.inc.php';
  
  if ($importo == $oldimporto) { 
    // si paga tutto gente!
    $link = mysqli_connect($dbhost,$dbuser,$dbpass);
    @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
    $sql = "Update Movimenti SET Pagato = 'si' where IDMovimento = '$id_movimento'";
    mysqli_query($link,$sql) or die("Errore nella Update Movimenti # importo = oldimporto # " . mysqli_error($link));
    logga("$user: Pagato Movimento n. $id_movimento." );
    mysqli_close();
  } elseif ($importo < $oldimporto && $importo > 0) {
    $movimento = Query_Movimento($id_movimento);
    // si paga un po' alla volta... incomincio con il modificare il valore iniziale e lo setto come pagato
    $link = mysqli_connect($dbhost,$dbuser,$dbpass);
    @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
    $sql = "update Movimenti SET Pagato = 'si', Importo='$importo' where IDMovimento = '$id_movimento'";
    logga("$user: Pagato Parzialmente il Movimento n. $id_movimento. Pagato $importo." );
    mysqli_query($link,$sql) or die("Errore nella Update Movimenti # importo=$importo < oldimporto=$oldimporto , id_mov=$id_movimento # "  . mysqli_error($link));
    // aggiungo un nuovo record del tutto uguale a quello di prima ma con l' importo piu' basso
    $newimporto = ($movimento['Importo'] - $importo);
    $sql = "Insert into Movimenti (Utente,Creditore,IDSpesa,Causale,Importo,Pagato) VALUES ('$movimento[Utente]', '$movimento[Creditore]', '$movimento[IDSpesa]', '$movimento[Causale]', '$newimporto','no')";
    logga("$user: Rimanente da pagare da $movimento[Utente] a $movimento[Creditore]: $newimporto." );
    mysqli_query($link,$sql) or die("Errore nella Insert into Movimenti ". mysqli_error($link) );
    mysqli_close();
  } else die ("Non puoi pagare piu' di quello che devi!");
} 

/** Aggiorna un record nella tabella spesa come pagato */
function Paga_Spesa($id_spesa,$user="nouser"){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "Update Spese SET Pagato = 'si' where IDSpesa = '$id_spesa'";
  mysqli_query($link,$sql) or die("Errore nella Update Spesa # importo = oldimporto # " . mysqli_error($link));
  logga("$user: Pagata Spesa n. $id_spesa.");
  mysqli_close($link);
}

/** Riceve le informazioni su di un pagamento */
function Query_Movimento($IDMovimento){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "select * from Movimenti where IDMovimento = '$IDMovimento';";
  $result = mysqli_query($link,$sql) or die("Errore nella query");
  $array = mysqli_fetch_array($result, MYSQLI_ASSOC);
  mysqli_close();
  return $array;
}


/** Controlla l' esistenza dell' utente e verifica la password */
function Query_Utente($user,$md5password){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "select * from Utenti where Username='$user' and Password='$md5password'";
  $results_all = mysqli_query($link,$sql) or die("Errore nella query");
  $result = mysqli_fetch_array($results_all);
  mysqli_close($link);
  if ($result['IDUtente'] > 0) return true;
  return false;
}

/** Inserisce un nuovo utente */
function Insert_Utente($user,$password){
  include 'config.inc.php';
  //include 'logs.inc.php';
  $password=md5($user . $password);
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "Insert into Utenti (Username,Password) VALUES ('$user','$password')";
  mysqli_query($link,$sql) or die("Impossibile aggiungere l' utente");
  logga("Inserito nuovo utente: $user");
  mysqli_close($link);
  return true;
}

/** Restituisce una field list per i form di nome 
       $name,per i possibili risultati della ricerca
       del campo $field e della tabella $table       */

function form_list($table,$field,$name,$any="Qualsiasi"){
  include 'config.inc.php';
  print "<select name=\"$name\">";
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql ="select $field, count(*) as Num from $table where 1 group by $field order by Num DESC limit 10;";
  $result = mysqli_query($link,$sql) or die("Errore nella query");
  print "<option selected=\"selected\" value=\"qualsiasi\">$any</option>";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
    print "<option value=\"" . $line[$field]. "\">". $line[$field]."</option>";
  print "</select>";
  mysqli_close($link);
}

function radio_list($table,$prefix,$field,$utente=null){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql ="select * from $table where Username != '$utente';";
  $result = mysqli_query($link,$sql) or die("Errore nella query");
  $i = 0;
  print "<div class=\"form-check\">\n";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)){
    print "<label class=\"form-check-label\">\n<input class=\"form-check-input\" type=\"checkbox\" name=\"$prefix$i\" value=\"$line[$field]\" checked>&nbsp$line[$field]\n</input>\n</label>\n<br>\n";
    $i++;    
  }
  print "<input type=\"hidden\" name=\"num_$prefix\" value=\"$i\">";
  print "</div>";
  mysqli_close($link);
}


/** Restituisce una field list per i form di nome 
       $name,per i possibili risultati della ricerca
       del campo $field e della tabella $table       */

function form_data($fieldg,$fieldm,$fielda){
  include 'config.inc.php';
  $anno = date("Y");
  $mese = date("m");
  $giorno = date("d");
  print "<select name=\"$fieldg\"  style='width: 60px'>";
  for($i=1;$i<=31;$i++){
    if ($i == $giorno) print "<option selected=\"selected\" value=\"$i\">$i</option>";
    else print "<option value=\"$i\">$i</option>";
  } print "</select>";
  print "<select name=\"$fieldm\" style='width: 60px'>";
  for($i=1;$i<=12;$i++){
    if ($i == $mese) print "<option selected=\"selected\" value=\"$i\">$i</option>";
    else print "<option value=\"$i\">$i</option>";
  } print "</select>";
  print "<select name=\"$fielda\" style='width: 120px' >";
  for($i=$anno;$i<=($anno+5);$i++){
    if ($i == $mese) print "<option selected=\"selected\" value=\"$i\">$i</option>";
    else print "<option value=\"$i\">$i</option>";
  } print "</select>";
}

/** Restituisce una tabella bidimensionale contenente
    Una ricerca del database movimenti  */

function Search_Movimenti($idmovimento,$idspesa,$utente,$causale,$pagato,$limit,$ordina,$user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "SELECT * FROM Movimenti WHERE ";
  $modificato = false;
  if ($idmovimento != "" && $idmovimento != null) { $query = $query . "IDMovimento='$idmovimento' "; $modificato = true; }
  
  if ($idspesa != "" && $idspesa != null ) { 
    if (!$modificato) { $query = $query . "IDSpesa='$idspesa' "; $modificato = true; }
    else { $query = $query . "AND IDSpesa='$idspesa' "; $modificato = true; }
  }
  if ($utente != "" && $utente != null && $utente != "qualsiasi") {
    if(!$modificato) { $query = $query . "Utente='$utente' "; $modificato = true; }
    else { $query = $query . "AND Utente='$utente' "; $modificato = true; }
  }
  if ($causale != "qualsiasi" && $causale != null ) {
    if(!$modificato) { $query = $query . "Causale='$causale' "; $modificato = true; }
    else { $query = $query . "AND Causale='$causale' "; $modificato = true; }
  }
  if ($pagato != "qualsiasi" && $pagato != null ) {
    if(!$modificato) { $query = $query . "Pagato='$pagato'"; $modificato = true; }
    else { $query = $query . "AND Pagato='$pagato'"; $modificato = true; }
  }
  if (! $modificato) $query = $query . "1";
  if ($ordina != null) $query = $query . " ORDER BY IDMovimento DESC";
  if ($limit != null) $query = $query . " LIMIT $limit";
  $query = $query . ";";
  $result = mysqli_query($link,$query) or die('Query ' . $query . ' failed: ' . mysqli_error($link));
  $result_cols = mysqli_query($link,"SHOW COLUMNS FROM Movimenti;");
  for($i=0;$i<mysqli_num_fields($result);$i++)
    {$cols[] = mysqli_result($result_cols, $i);
    }
  // Printing results in HTML
  print "<fieldset>\n";
  echo "<b>Query: $query</b><br><br>";
  print "<div class='container'><table class=\"table table-hover\" >";
  $line = mysqli_fetch_array($result_cols, MYSQLI_ASSOC);
  foreach ($cols as $col_value) {
    echo "\t\t<th align='center'>$col_value</th>\n";
  }
  echo "\t\t<th align='center'>&nbsp;</th>\n";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
      if ($col_value == $line['Importo']){
	echo "\t\t<td> ";
	printf("%.2f",$col_value);
	echo "</td>\n"; } elseif ($col_value == $line['IDSpesa']){
	  echo "\t\t<td> ";
	  echo "<a href='search.php?type=spesa&idspesa=$col_value'>$col_value</a>";
	  echo "</td>\n"; } else  {
	    echo "\t\t<td> $col_value</td>\n"; 
	  }
    }
    echo "\t\t<td>";
    if (($line['Creditore'] == $user || $line['Utente'] == $user) && $line['Pagato'] == "no") {
      echo "<a href='paga.php?type=movimento&idmovimento=$line[IDMovimento]'>Paga</a>";
    } 
    echo "</td>\n";
    echo "\t</tr>\n";
    
  }
  echo "</table>\n</div>";
  echo "</fieldset>";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
  
}

/** Restituisce una tabella bidimensionale contenente
    Una ricerca del database spese  */

function Search_Spesa($idspesa,$utente,$causale,$pagato,$limit,$ordina,$user){
  include 'config.inc.php';
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // Performing SQL query
  $query = "SELECT * FROM Spese WHERE ";
  $modificato = false;
  if ($idspesa != "" && $idspesa != null ) { $query = $query . "IDSpesa='$idspesa' "; $modificato = true; }
  
  if ($utente != "" && $utente != null && $utente != "qualsiasi") {
    if(!$modificato) { $query = $query . "Utente='$utente' "; $modificato = true; }
    else { $query = $query . "AND Utente='$utente' "; $modificato = true; }
  }
  if ($causale != "qualsiasi" && $causale != null ) {
    if(!$modificato) { $query = $query . "Causale='$causale' "; $modificato = true; }
    else { $query = $query . "AND Causale='$causale' "; $modificato = true; }
  }
  if ($pagato != "qualsiasi" && $pagato != null ) {
    if(!$modificato) { $query = $query . "Pagato='$pagato'"; $modificato = true; }
    else { $query = $query . "AND Pagato='$pagato'"; $modificato = true; }
  }
  if (! $modificato) $query = $query . "1";
  if ($ordina != null) $query = $query . " ORDER BY Scadenza DESC";
  if ($limit != null) $query = $query . " LIMIT $limit";
  $query = $query . ";";
  $result = mysqli_query($link,$query) or die('Query ' . $query . ' failed: ' . mysqli_error($link));
  $result_cols = mysqli_query($link,"SHOW COLUMNS FROM Spese;");
  for($i=0;$i<mysqli_num_fields($result);$i++)
    {$cols[] = mysqli_result($result_cols, $i);
    }
  // Printing results in HTML
  print "<fieldset>\n";
  echo "<b>Query: $query</b><br><br>";
  print "<div class='container'><table class=\"table table-hover\">";
  $line = mysqli_fetch_array($result_cols, MYSQLI_ASSOC);
  foreach ($cols as $col_value) {
    echo "\t\t<th align='center'> $col_value</th>\n";
  }
  echo "<th></th></thead>";
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    list($scad_a,$scad_m,$scad_g) = explode("-",$line['Scadenza'],3);
    if (mktime(0,0,0,$scad_m,$scad_g,$scad_a) <= mktime(0,0,0,date("m"),date("d"),date("Y")) && $line['Scadenza'] != null && $line['Pagato'] != "si") $color = "#FF0000";
    else $color = "#000000";
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
      echo "\t\t<td>";
      if ($color == "#FF0000") echo "<b>";
      if ($col_value == $line['IDSpesa']){

	echo "<a href='search.php?type=movimento&idspesa=$col_value'>$col_value</a>";
	echo "\n"; } else { 
	  echo "\t\t $col_value\n";
	  if ($color == "#FF0000") echo "</b>";
	}
      echo "</td>";
    }
    echo "\t\t<td>";
    if ($_COOKIE['user'] == $user && $line['Utente'] == $user && $line['Pagato'] == "no") { 
      echo "<a href='paga.php?type=spesa&idspesa=$line[IDSpesa]'>Paga</a>";
    } 
    echo "</td>\n";
    
    echo "\t</tr>\n";
    echo "</td>\n";
    echo "\t</tr>\n";
    
  }
  echo "</table></div>\n";
  echo "</fieldset>";
  // Free resultset
  mysqli_free_result($result);
  
  // Closing connection
  mysqli_close($link);
  
}
/** Cambia Password */
function Cambia_Password($user,$oldpassword,$newpassword){
  include 'config.inc.php';
  //include 'logs.inc.php';
  $newpassword = md5($user . $newpassword);
  $oldpassword = md5($user . $oldpassword);
  if (! Query_Utente($user,$oldpassword)) { PrintLineTable("Password Errata!"); return false; }
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die( "Unable to select database: $dbhost,$dbuser,$dbpass,$dbname");
  $sql = "Update Utenti set Password = '$newpassword' where Username='$user' and Password='$oldpassword'";
  $results_all = mysqli_query($link,$sql) or die("Errore nell' Aggiornamento della Password.");
  logga("$user: Password Cambiata");
  mysqli_close($link);
  return true;
}
?>
