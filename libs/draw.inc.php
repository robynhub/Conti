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
 * Libreria per il disegnamento delle tabelle 
 *
 *  PrintLineTable($title)
 *  DrawMenu($user)
 */
 
 
 	function DrawHeader($home=false){
	 	include 'config.inc.php';
	 	?>

<head>

  <meta content="text/html;charset=ISO-8859-1" http-equiv="Content-Type">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php if ($home) {
	  echo "<meta name=\"viewport\" content=\"width=714px, maximum-scale=1\">";
  } else {  
	 echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=1\">";
 } ?>
  
  <title>Conti ver. <?php print $ver; ?></title>
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">


    <style type="text/css">
                body {
                        padding-top: 40px;
                        padding-left: 0px;
                        padding-right: 0px;
                        background-color: #eee;
                        
                        }

        .hero-unit { background-color: #fff; }
        
        .container-fluid { padding: 2px; }
        
        .navbar-inverse .navbar-nav .open .dropdown-menu > li > a {
	        	color: #ffffff;
        }
        
        
        .navbar-inverse .navbar-nav .open .dropdown-menu > li > a:hover {
	        background-color: #337ab7;
        }
        
        .dropdown-menu {
	        background-color: #000000;
        }
        
        .navbar-inverse .navbar-nav > .open > a, 
.navbar-inverse .navbar-nav > .open > a:hover, 
.navbar-inverse .navbar-nav > .open > a:focus {

    background-color: #337ab7;
}



                </style>

  <meta content="Antonio Bartolini" name="author">

</head>
	 	
	 	
	 	<?php
 	}

################################################################################
# Funzione: Printlinetable($Title)                                             #
# Costruisce Una Tabella Di Una Riga                                           #
# Posizionando Al Centro Il Titolo                                             #
# Passato Per Argomento                                                        #
################################################################################

    function PrintLineTable($title) {
       print "<table width='80%' class='table'>\n";
       print "  <tr>\n";
       print "    <td width='40%'>\n";
       print "     <div align='center'>
                      <h3>
                          $title
                        </font>
                      </h3>
                      </div>\n";
       print "    </td>\n";
       print "  </tr>\n";
       print "</table>\n";
    }


################################################################################
# Funzione: DrawMenu()                                                         #
# Disegna il menu con i vari links...                                          #
#                                                                              #
################################################################################
 function DrawMenu($user){
 include 'config.inc.php';
 ?>

 

   <!-- Static navbar -->
      <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="main.php"><b>&nbsp;&nbsp;&nbsp;Conti Ver. <?php echo $ver; ?></b></a>
          </div>
          <div id="navbar" class="navbar-collapse collapse navbar-right">
            <ul class="nav navbar-nav" >
	          <li class="active" ><a href="main.php">Home</a></li>  
              <li class="dropdown" style="background-color: #337ab7;">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #ffffff;">Inserisci <span class="caret"></span></a>
                <ul class="dropdown-menu" style="background-color: #337ab7;">
                  <li><a  href="insert_spesa.php">Aggiungi Bolletta</a></li>
                  <li><a  href="insert_pagamento.php">Aggiungi Pagamento</a></li>
                </ul>
	          </li>
	          
              <li class="active"><a style="background-color: #337ab7;" href="search.php">Ricerca</a></li>
              <li class="active"><a style="background-color: #5cb85c;" href="tornaconti.php">Tornaconti</a></li>
              <li class="active"><a style="background-color: #337ab7;" href="logs.php">Logs</a></li>
              
              <li class="dropdown"  style="background-color: #337ab7;">
	            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="color: #ffffff;">User<span class="caret"></span></a>
                <ul class="dropdown-menu" style="background-color: #337ab7;">
                  <li><a  href="chpassw.php">Cambia Password</a></li>
                  <li><a  href="logout.php">Logout</a></li>
                </ul>
              </li>
              
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
<!--
<nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
                <div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                      <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				    </button>
                    <a  href='main.php'><p class="navbar-text"><b>Conti Ver. <?php echo $ver; ?><//b></p></a>
                    
				</div>	
					  
                 <div class="collapse navbar-collapse">
                    <ul class="nav pull-right"> 
                         
                        <div class="btn-group">
                                <a href="main.php" class="btn btn-primary" >Home</a>

                        </div> 
                        <div class="btn-group">
	                          <a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Inserisci <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                        <li><a href="insert_spesa.php">Aggiungi Bolletta</a></li>
                                        <li><a href="insert_pagamento.php">Aggiungi Pagamento</a></li>
                        </div>
                        <div class="btn-group">
                                <a href="search.php" class="btn btn-primary" >Ricerca</a>

                        </div>
                        <div class="btn-group">
                                <a href="tornaconti.php" class="btn btn-success" >Tornaconti</a>

                        </div>
                        <div class="btn-group">
                                <a href="logs.php" class="btn btn-primary" >Logs</a>

                        </div>
                        <div class="btn-group">
                                <a href="#" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">User <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                        <li><a href="chpassw.php">Cambia Password</a></li>
                                        <li><a href="logout.php">Logout</a></li>

                        </div>
                        <div class="btn-group">
						<li><p class="navbar-text">Logged as: <?php print $user; ?></p></li>
                        </div>
                </div>
                
        </div>
</nav>
-->
<?php
 }

?>
