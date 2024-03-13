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

// funzione di debug
function ul_var_dump(&$var,$type=0)
{
  if(!$type)
    echo "<ul type='circle' style='border:1px solid #a0a0a0;padding-bottom:4px;padding-right:4px'>\n<li>";
  if(is_array($var))
    {
      echo "[array][".count($var)."]";
      echo "<ul type='circle' style='border:1px solid #a0a0a0;padding-bottom:4px;padding-right:4px'>\n";
      foreach($var as $k=>$v)
	{
	  echo "<li>\"{$k}\"=>";
	  ul_var_dump($v,1);
	}
      echo "</ul>\n";
    }
  else
    echo "[".gettype($var)."][{$var}]</li>\n";
  if(!$type)
    echo "</ul>\n";
}

// restituisce un array con tutte le query necessarie per 
// applicare il tornaconto
function SQL_Applica_Modifiche($tornaconto_finale){
  include 'config.inc.php';
  // preparo la connessione al db
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // creo il form per l' applicazione delle modifiche
  
  $risultati = array();
  foreach($tornaconto_finale as $torna){
    $query = "select sum(Importo) as Debito from Movimenti where Utente = '$torna[1]' and Creditore = '$torna[2]' and Pagato = 'no';";
    $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
    $debito = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $query = "select * from Movimenti where Utente='$torna[1]' and Creditore = '$torna[2]' and Pagato='no' order by Importo;";
    $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
    while($movimento =   mysqli_fetch_array($result, MYSQLI_ASSOC)){
      
      if ( $debito[Debito] == $torna[0]) break;
      
      if ( $debito[Debito] < $torna[0] ){ // alzo il debito
	$newvalue = $torna[0] - $debito[Debito];
	array_push($risultati,"INSERT INTO Movimenti (Utente,Creditore,IDSpesa,Causale,Importo,Pagato) VALUES ('$movimento[Utente]','$movimento[Creditore]','0','Tornaconto','$newvalue','no');");
	$debito[Debito] = $torna[0];
      } elseif( $debito[Debito] - $movimento[Importo] + 0.1  >= $torna[0]) //abbasso il debito: Il movimento va segnato pagato?
	{
	  array_push($risultati,"UPDATE Movimenti SET Pagato = 'si' WHERE IDMovimento = '$movimento[IDMovimento]' and Utente = '$movimento[Utente]';");
	  $debito[Debito] -= $movimento[Importo];
	  
	} else { // il movimento e' troppo salato per quello che devo pagare. Pago solo quello che devo.
	  $newvalue = $movimento[Importo] - ($debito[Debito] - $torna[0]);
	  array_push($risultati,"UPDATE Movimenti SET Importo = '$newvalue' WHERE IDMovimento = '$movimento[IDMovimento]' and Utente = '$movimento[Utente]';");
	  $newvalue = $movimento[Importo] - $newvalue;
	  array_push($risultati,"INSERT INTO Movimenti (Utente,Creditore,IDSpesa,Causale,Importo,Pagato) VALUES ('$movimento[Utente]','$movimento[Creditore]','$movimento[IDSpesa]','$movimento[Causale]','$newvalue','si');");
	  $debito[Debito] = $torna[0];
	}
    }
    
  } 
  mysqli_free_result($result);
  mysqli_close($link);
  return $risultati;
  
}

// toglie tutti i cicli del grafo e ribilancia il tutto
function Tornaconto_1st_pass($tabella,$utenti){
  foreach ($utenti as $utente){
    do {
      $risultato = Rec_Tornaconto($tabella,$utente);
      $tabella = $risultato[0];
    } while ($risultato[1]);
    
  }
  return $tabella;
}

// DFS modificata. Naviga il grafo senza passare dove e' 
// gia' passato e quando trova un ciclo torna indietro
// togliendo il valore piu' basso di tutti gli archi
// incontrati fino a quel momento.
function Rec_Tornaconto($S,$utente,$da=null,$min=100000000,$ret=false,$Q=null){
  if ($da == null) $da = $utente;
  if ($Q == null) $Q = array(); // lo porto solo in discesa
  $debug = false; // turn to true to show debug info
  
  $nodo = $S[$da];
  foreach($nodo as $a => $arco){
    if ($S[$da][$a] != 0 && $S[$da][$a] < $min)
      $min = $S[$da][$a];
    
    if($S[$da][$a] != 0){
      if ($debug) print ("<br>Radice: $utente. Esamino da $da a $a. Minimo: $min");
      if ($utente == $a){ // trovato loop
	$S[$da][$a] -= $min;
	if ($debug) print "<br>Trovato loop sottraggo $min tra $da e $a";
	return array($S,true,$min); // torno indietro
	
      } else { // continuo la ricerca
	if (array_search($a,$Q) == null){
	  array_push($Q,$da);
	  // Iterare e' umano...
	  // Ricorrere e' divino!
	  $risultato = Rec_Tornaconto($S,$utente,$a,$min,false,$Q);
	  if ($risultato[1]){ // trovato loop!
	    $S = $risultato[0];
	    $min = $risultato[2];
	    if ($debug) print "<br>Trovato loop sottraggo $min  tra $da e $a";
	    $S[$da][$a] -= $min; 
	    return array($S,true,$min); // continuo il ritorno
	  }
	}
      }
    }
  }
  return array($S,false,$min);
}


// Semplifica alcuni tipi di cicli nel grafo
// ATTENZIONE il formato di output cambia!
function Tornaconto_2nd_pass($tabella,$utenti,$logga=false){
  // converto la tabella
  foreach($utenti as $da){
    foreach($utenti as $a){
      $tornaconto[$i][0] = $tabella[$da][$a];
      $tornaconto[$i][1] = $da;
      $tornaconto[$i][2] = $a;
      $i++;
    }
  }
  // se ho meno di 2 utenti e' inutile continuare
  if (count($utenti) < 2) 
    return $tornaconto;
    
  // applico il secondo passo
  sort($tornaconto);
  $num = count($tornaconto);
  
  do {
    
    $ischanged = false;
    
    // $tornaconto[][0] = valore
    // $tornaconto[][1] = da
    // $tornaconto[][2] = a
    //
    //     i       j       h
    //    C->A    A->B    C->B
    //
    // oppure
    //
    //     i       j       h
    //    A->B    C->A    C->B
    // 
    // Scusate la porcata.
    
    for ($i=0 ; $i<$num ; $i++){
      for ($j=0 ; $j<$num ; $j++){
	for ($h=0 ; $h<$num ; $h++){
	  if (($tornaconto[$i][0] != 0 && 
	       $tornaconto[$j][0] != 0 && 
	       $tornaconto[$h][0] != 0 &&
	       $tornaconto[$j][0] >= $tornaconto[$i][0] &&
	       $tornaconto[$i][2] == $tornaconto[$j][1] &&
	       $tornaconto[$j][2] == $tornaconto[$h][2] &&
	       $tornaconto[$i][1] == $tornaconto[$h][1] &&
	       $tornaconto[$i][1] != $tornaconto[$j][1] &&
	       $tornaconto[$i][1] != $tornaconto[$h][2] &&
	       $tornaconto[$i][1] != $tornaconto[$j][2] &&
	       $tornaconto[$i][2] != $tornaconto[$j][2] &&
	       $tornaconto[$i][2] != $tornaconto[$h][1] &&
	       $tornaconto[$i][2] != $tornaconto[$h][2] &&
	       $tornaconto[$j][2] != $tornaconto[$h][1] &&
	       $tornaconto[$j][1] != $tornaconto[$h][1] &&
	       $tornaconto[$j][1] != $tornaconto[$h][2]) 
	      || 
	      ($tornaconto[$i][0] != 0 && 
	       $tornaconto[$j][0] != 0 && 
	       $tornaconto[$h][0] != 0 &&
	       $tornaconto[$j][0] >= $tornaconto[$i][0] &&
	       $tornaconto[$i][2] == $tornaconto[$h][2] &&
	       $tornaconto[$j][2] == $tornaconto[$i][1] &&
	       $tornaconto[$j][1] == $tornaconto[$h][1] &&
	       $tornaconto[$i][1] != $tornaconto[$j][1] &&
	       $tornaconto[$i][1] != $tornaconto[$h][2] &&
	       $tornaconto[$i][1] != $tornaconto[$h][1] &&
	       $tornaconto[$i][2] != $tornaconto[$j][1] &&
	       $tornaconto[$i][2] != $tornaconto[$h][1] &&
	       $tornaconto[$i][2] != $tornaconto[$j][2] &&
	       $tornaconto[$j][2] != $tornaconto[$h][2] &&
	       $tornaconto[$j][2] != $tornaconto[$h][1] &&
	       $tornaconto[$j][1] != $tornaconto[$h][2] ) && 
	      $i != $j && $i != $h && $j != $h){
	    $tornaconto[$h][0] += $tornaconto[$i][0];
	    $tornaconto[$j][0] -= $tornaconto[$i][0];
	    $tornaconto[$i][0] -= $tornaconto[$i][0];
	    $ischanged = true;
	  }
	}
      }
    }
  } while ($ischanged);
  return $tornaconto;
}

// ritorna un array con tutti gli utenti
function Get_Utenti(){
  include 'config.inc.php';
  // preparo la connessione al db
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  
  // preparo la lista degli utenti
  
  $query = "select Username from Utenti where 1;";
  $result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
  $i=0;
  while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $utenti[$i] = $line[Username];
    $i++;
  }
  mysqli_free_result($result);
  mysqli_close($link);
  return $utenti;
  
}

// restituisce la tabella con i debiti
function Get_Tabella($utenti,$logga=false){
  include 'config.inc.php';
  $thereissomeone = false;
  // preparo la connessione al db
  $link = mysqli_connect($dbhost,$dbuser,$dbpass);
  @mysqli_select_db($link,$dbname) or die('Could not select database');
  // preparo la tabella con tutti i debiti e i movimenti
  foreach($utenti as $da){
    foreach($utenti as $a){
      if ($da == $a ){ $tabella[$da][$a] = 0; } else {
	$query = "select sum(Importo) as Importo from Movimenti where Utente='$da' and Creditore = '$a' and Pagato='no';";
	$result = mysqli_query($link,$query) or die('Query failed: ' . mysqli_error($link));
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if ($line[Importo] > 0) {
	  $tabella[$da][$a] = $line[Importo];
	  $thereissomeone = true;
	}
	mysqli_free_result($result);
      }
    }
  }
  $situazione = array();
  $num_sit=0;
  
  //print "<table><tbody><tr><td>";
  print "<h2>Attuale:</h2>";
  //print "</td></tr></tbody></table></div>";
  
  if ($logga == true){
    logga("=== Situazione Attuale: ===");
  }
  foreach($utenti as $da){
    foreach($utenti as $a){
      if ($tabella[$da][$a] == null) $tabella[$da][$a] = 0;
      $simp = number_format($tabella[$da][$a],2);
      if ($simp != 0){ 
	print "$da deve a $a $simp euro.<br>";
	if ($logga == true)
	  logga("$da deve a $a $simp euro.");
	array_push($situazione,array($tabella[$da][$a],$da,$a));
	$num_sit++;
      }
    }
  }
  mysqli_close($db);
  if ($thereissomeone) {  // FIX:  DoS Bug! 
    return array($tabella,$situazione);
  } else {
    print "Nessun movimento disponibile.";
    exit;
  }
}

// usa dot per creare il grafico e inserisce il tag html
function draw_tornaconto($tornaconto,$name){
  $dotpath = exec("which dot",$out,$return);
  if ($return == 0){
    $buffer = "\"digraph G {  ";
    foreach ($tornaconto as $torna){
      if ($torna[0] != 0){
	$value = number_format($torna[0],2);
	$buffer = $buffer . "$torna[1] -> $torna[2] [label=\\\" $value €\\\"];";
      }
    }
    $buffer = $buffer . "}\"";
    system("echo $buffer | $dotpath -T jpg -o $name",$return);
    print "<br><img src=\"$name\">";
  }
}
