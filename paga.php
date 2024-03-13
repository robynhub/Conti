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
if (!($_COOKIE['user'] && $_COOKIE['pass']) ||  !($_GET['type'] == "movimento" || $_GET['type'] == "spesa" || $_GET['type'] == "internal") ) {
  header("Location: index.php");
} elseif (! query_utente($_COOKIE['user'],$_COOKIE['pass'])){
  header("Location: index.php");
  header("Pragma: no-cache");
}
$user = $_COOKIE['user']; $password = $_COOKIE['pass'];
/** Fine controllo accessi */

/** eseguiamo le operazioni */
if ($_GET['type'] == "internal") Paga_Movimento($_GET['id'],$_GET['Importo'],$_GET['oldImporto'],$_COOKIE['user']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<?php DrawHeader(); ?>
<body> 
<?php DrawMenu($user);
echo "<br>";
if ($_GET['type'] == "movimento"){
  // Form Paga Movimento
  $data = Query_Movimento($_GET['idmovimento']);
    ?>
    <form method="get" action="paga.php" name="Spesa">
       <table style="text-align: left; width: 100%;" border="0"
       cellpadding="2" cellspacing="2">
       <tbody>
       <tr>
       <td style="width: 102px; height: 85px;"></td>
       <td style="width: 72px; height: 85px;"></td>
       <td style="height: 85px; width: 262px;"></td>
       <td style="height: 85px; width: 329px;"></td>
       </tr>
       <tr>
       <td style="width: 102px; height: 0px;"></td>
       <td
       style="width: 72px; vertical-align: top; text-align: right; height: 0px;"><big><span
       style="font-family: Helvetica,Arial,sans-serif;">Causale:&nbsp;</span></big></td>
									  <td
									  style="vertical-align: top; height: 0px; font-family: Helvetica,Arial,sans-serif; width: 262px;"><big><?php
									  echo $data['Causale']; ?></big></td><input name="id" type="hidden" value="<?php echo $data['IDMovimento']; ?>">
												     <td style="height: 0px; width: 329px;"></td>
												     </tr>
												     <tr>
												     <td></td>
												     <td style="text-align: right;"><big><span
												     style="font-family: Helvetica,Arial,sans-serif;">Creditore:&nbsp;</span></big></td>
																					  <td
																					  style="font-family: Helvetica,Arial,sans-serif; width: 262px;"><big><?php
																					  echo $data['Creditore']; ?><br>
																								      </big></td>
																								      <td style="width: 329px;"></td>
																								      </tr>
																								      <tr>
																								      <td></td>
																								      <td style="text-align: right;"><big><span
																								      style="font-family: Helvetica,Arial,sans-serif;">Debitore:&nbsp;</span></big></td>
																																	  <td
																																	  style="font-family: Helvetica,Arial,sans-serif; width: 262px;"><big><?php
																																	  echo $data['Utente']; ?><br>
																																				   </tr>
																																				   <tr>
																																				   <td></td>
																																				   <td style="text-align: right;"><big><span
																																				   style="font-family: Helvetica,Arial,sans-serif;">IDSpesa:</span></big></td>
																																				   <td
																																				   style="font-family: Helvetica,Arial,sans-serif; width: 262px;"><big><?php
																																				   echo $data['IDSpesa']; ?></big></td><input name="oldImporto" type="hidden" value="<?php echo $data['Importo']; ?>">
																																							      
																																							      <td style="width: 329px;"></td>
																																							      </tr>
																																							      <tr>
																																							      <td></td>
																																							      <td style="text-align: right;"><big><span
																																							      style="font-family: Helvetica,Arial,sans-serif;">Importo:&nbsp;</span></big></td>
																																																 <td style="width: 262px;"><input
																																																 value="<?php echo $data['Importo']; ?>" name="Importo"></td>
																																																 <td style="width: 329px;"></td><input type="hidden" name="type"
																																																 value="internal">
																																																 </tr>
																																																 <tr>
																																																 <td></td>
																																																 <td style="text-align: right;"><big><span
																																																 style="font-family: Helvetica,Arial,sans-serif;"><br>
																																																 </span></big></td>
																																																 <td style="width: 262px;">
																																																 <div style="text-align: center;"><button
																																																 name="Invia" value="Invia" class="btn  btn-primary">Paga</button></div>
																																																 </td>
																																																 <td style="width: 329px;"></td>
																																																 </tr>
																																																 <tr>
																																																 <td style="width: 102px;"></td>
																																																 <td style="width: 262px;" colspan="2">
																																																 <div style="text-align: center;"><br>
																																																 </div>
																																																 </td>
																																																 <td style="width: 329px;"></td>
																																																 </tr>
																																																 </tbody>
																																																 </table>
																																																 <br>
																																																 </form>
																																																 
																																																 
																																																 <?php 
																																																 } else {
  //Form Paga Spesa No Form!
  if ($_GET['type'] == "spesa")
    Paga_Spesa($_GET['idspesa'],$user);
  PrintLineTable("Spesa Pagata!");
  
    ?>
    
    <?php
	} //endif
?>
</body>
</html>
