<!DOCTYPE HTML>
<?php
require_once("menu.php");
require_once("dialogs.php");
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

?>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.gif" type="image/gif">
    <title>Editor di Pagine</title>

    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/forms-min.css">
    <link rel="stylesheet" href="css/font-awesome-4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/site.home.css">
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
    <script type="text/javascript" src="tinymce/tinymce.min.js"></script>
    <link rel="stylesheet" href="jquery-ui/jquery-ui.css">
    <script src="jquery-ui/jquery-ui.js"></script>

</head>

<body>
<div id="layout">
    <!-- Menu toggle -->
    <a href="#menu" id="menuLink" class="menu-link">
        <!-- Hamburger icon -->
        <span></span>
    </a>

    <div id="menu">
        <?php print_menu("Gestione Menu"); ?>
    </div>

    <div id="main">
        <div id="toolbar">
            <h1 style="text-align: center; margin-top: 0; color: white;">Nuova Pagina</h1>
        </div>

        <!-- INIZIO AREA EDITING MENU -->
            <ul>
                <li level="0">Primo</li>
                <li level="0">Secondo</li>
                <li level="0">Terzo</li>
                <li level="0" class="new-li">Nuovo</li>
                <li style="" id="li-template">
                    <span class="label-caption">Label</span>
                    <input type="hidden" name="label[]">
                    <input type="hidden" name="url[]">
                    <input type="hidden" name="order[]">
                    <input type="hidden" name="level[]">
                    <a href="#"><i class="fa fa-pencil-square fa-lg"></i></a>
                    <a href="#"><i class="fa fa-minus-square fa-lg"></i></a>
                    <ul>
                        <li class="new-li">Nuovo</li>
                    </ul>
                </li>
            </ul>
        <!-- FINE AREA EDITING MENU -->

    </div>

</body>
</html>