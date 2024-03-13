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
$time = time();
 logga("Logout Utente: $_COOKIE[user]");
 setcookie ("user", $_COOKIE[user], $time-3200); 
 setcookie ("pass", $_COOKIE[pass], $time-3200);
 header("Location: index.php");

?> 
