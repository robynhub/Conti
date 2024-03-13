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
   include "libs/config.inc.php";
   $time = time();
   $user = "";
   $pass = "";
   if (isset ($_POST['user']) && $_POST['user'] && $_POST['pass']) {
      $user = $_POST['user'];
      $pass = md5($user . $_POST['pass']);
   } elseif (isset ($_COOKIE['user']) && $_COOKIE['user'] && $_COOKIE['pass']) {
      $user = $_COOKIE['user'];
      $pass = $_COOKIE['pass'];
   }
      if (query_utente($user,$pass)) {
         setcookie ("user", $_POST['user'],$time+3200);
         setcookie ("pass", md5($user . $_POST['pass']),$time+3200);
	 logga("Login Utente: $user");
         header("Location: main.php"); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Refresh" content="2;main.php">
<meta content="Antonio Bartolini" name="author">
</head>
<body style="color: rgb(0, 0, 0); background-color: rgb(59, 203, 242);" alink="#134c97" link="#000099" vlink="#3c17ba">
Redirecting...
<?php
  } else { 
  
?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

  <meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
  <title>Wellcome - Conti ver. <?php print $ver; ?></title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">

    <link rel="stylesheet" type="text/css" href="css/jquery-gmaps-latlon-picker.css"/>
    <script src="js/jquery-gmaps-latlon-picker.js"></script>

    <style type="text/css">
body {
  padding-top: 40px;
  padding-bottom: 40px;
  background-color: #eee;
}

.form-signin {
  max-width: 330px;
  padding: 15px;
  margin: 0 auto;
}
.form-signin .form-signin-heading,
.form-signin .checkbox {
  margin-bottom: 10px;
}
.form-signin .checkbox {
  font-weight: normal;
}
.form-signin .form-control {
  position: relative;
  height: auto;
  -webkit-box-sizing: border-box;
     -moz-box-sizing: border-box;
          box-sizing: border-box;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
                </style>

  <meta content="Antonio Bartolini" name="author">

</head>
<body>
<?php
if (isset($_POST['user']) && $_POST['user'] && $_POST['pass']) print "Username non riconosciuto";
?>

    <div class="container">

      <form class="form-signin" action="index.php" method="post">
        <h2 class="form-signin-heading">Effettua il Login</h2>
        <label for="lg_username" class="sr-only">Username</label>
        <input type="text" id="user" name="user" class="form-control" placeholder="Username" required autofocus>
        <label for="inputPassword" id="passlabel" class="sr-only">Password</label>
        <input type="password" id="pass" name="pass" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" value="Login" type="submit">Entra</button>
      </form>

    </div> <!-- /container -->





<br>
<?php
}
?>
<script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
