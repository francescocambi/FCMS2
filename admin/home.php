<!DOCTYPE HTML>
<?php
require_once("menu.php");

//TODO: Verify html session
?>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="favicon.gif" type="image/gif">
<title>Admin Area Home</title>

<!-- <script src="js/jquery-2.1.1.min.js"></script> -->
<link rel="stylesheet" href="css/pure-min.css">
<!-- <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/tables-min.css"> -->
<!-- <link rel="stylesheet" href="jquery-ui/jquery-ui.css">
<script src="jquery-ui/jquery-ui.js"></script>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet"> -->
<link rel="stylesheet" href="css/site.home.css">

</head>


<body>

<div id="layout">
    <!-- Menu toggle -->
    <a href="#menu" id="menuLink" class="menu-link">
        <!-- Hamburger icon -->
        <span></span>
    </a>

    <div id="menu">
        <?php print_menu("Home"); ?>
    </div>

    <div id="main">
    	<div id="toolbar">
    		<h1>Benvenuto!</h1>	
    	</div>
        <h2 style="text-align: center; margin-top: 40px; font-weight: lighter;">Usa il men&ugrave; a sinistra per selezionare l'operazione desiderata.</h2>
    </div>
</div>


<script src="js/menu.js"></script>

</body>
</html>

