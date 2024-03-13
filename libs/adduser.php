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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

  <meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
  <title>Conti ver. 0.1</title>


  <meta content="Antonio Bartolini" name="author">

</head>
<body style="color: rgb(0, 0, 0); background-color: rgb(59, 203, 242);" alink="#134c97" link="#000099" vlink="#3c17ba">

<?php 
   include "db.inc.php";
   if ($_POST[user] && $_POST[pass]) {
   insert_utente($_POST[user],$_POST[pass]) or die ("Impossibile aggiungere l' utente");
   print "Utente Inserito";
   }
?> 
<table style="text-align: left; width: 100%; height: 100%;" border="0" cellpadding="0" cellspacing="0">

  <tbody>

    <tr>

      <td style="height: 210px; width: 251px;"></td>

      <td style="height: 210px; width: 246px;"></td>

      <td style="height: 210px; width: 298px;"></td>

    </tr>

    <tr>

      <td style="height: 195px; width: 251px;"></td>

      <td style="text-align: left; vertical-align: top; height: 195px; width: 246px;">
      <form action="adduser.php" method="post">
        <table style="font-family: arial; font-size: 12px;" align="center">

          <tbody>

            <tr>

              <td colspan="2" align="center" bgcolor="#123dd4">Aggiungi un Utente</td>

            </tr>

            <tr>

              <td align="right">Username: </td>

              <td><input name="user" size="15" type="text"></td>

            </tr>

            <tr>

              <td align="right">Password: </td>

              <td><input name="pass" size="15" type="password"></td>

            </tr>

            <tr>

              <td colspan="2" align="center"><input value="Login" type="submit"></td>

            </tr>

          </tbody>
        </table>

      </form>

      </td>

    </tr>

    <tr>

      <td style="height: 217px; width: 251px;"></td>

      <td style="height: 217px; width: 246px;"></td>

      <td style="height: 217px; width: 298px;"></td>

    </tr>

  </tbody>
</table>

<br>
</body>
</html>
