<?php
/**
 * User: francesco
 * Date: 10/9/14
 * Time: 10:41 AM
 */
require_once("menu.php");
require_once("../bootstrap.php");
require_once("dialogs.php");
$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");
?>
<!DOCTYPE HTML>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.gif" type="image/gif">
    <title>Gestione Menu</title>

    <script src="js/jquery-2.1.1.min.js"></script>
    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/tables-min.css">
    <link rel="stylesheet" href="jquery-ui/jquery-ui.css">
    <script src="jquery-ui/jquery-ui.js"></script>
    <link href="css/font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
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
        <?php print_menu("Gestione Menu"); ?>
    </div>

    <div id="main">
        <div id="toolbar">
            <h1>Gestione Menu</h1>
        </div>
        <table class="pure-table pure-table-striped center" id="content-table" style="margin-top: 3em;">
            <thead>
            <tr style="font-size: 1.1em;">
                <th>ID</th>
                <th>Nome</th>
                <th>Descrizione</th>
                <th>Usato da</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <?php
            $menus = $em->getRepository('Model\Menu')->findAll();
            foreach ($menus as $menu) {
                echo "<tr><td class=\"idcell\">".$menu->getId()."</td><td>".$menu->getName()."</td><td>".$menu->getDescription()."</td>";
                //Prints language flags which use this menu
                echo "<td class=\"language-flags\">";
                foreach ($menu->getLanguages()->toArray() as $language) {
                    echo "<img src=\"../".$language->getFlagImageURL()."\" alt=\"".$language->getCode()."\">&nbsp;&nbsp;";
                }
                echo "</td>";
			    echo "<td><a href=\"modmenu.php?menuid=".$menu->getId()."\"><i class=\"fa  fa-pencil-square fa-lg\"></i></a><a class=\"delete\"><i class=\"fa fa-minus-square fa-lg\"></i></a></td></tr>";
            }
            ?>
                        <tr><td colspan="5" style="text-align: center;"><a href="modmenu.php"><i class="fa fa-plus-square fa-2x"></i></a></td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php deleteConfirmDialog(); ?>
<?php errorDialog(); ?>

<script src="js/menu.js"></script>
<script>
    /**
     * Generates and open a new jquery ui dialog with title and message
     * passed as arguments to display errors for the user
     * @param title
     * @param message
     */
    function displayErrorDialog(title, message) {
        var html = '<div class="message-error-dialog"><p>'+message+'</p>' +
            '<button type="button" class="pure-button pure-button-primary red-button" style="float: right;">Ok</button>' +
            '</div>';
        var dialog = $(html).dialog({
            title: title,
            modal: true,
            autoOpen: true
        });
        dialog.find("button").click(function() {
            dialog.dialog("close");
        });
    }
    $(".delete").click(function(event) {
        var id = $(event.target).parent().parent().parent().find("*:nth-child(1)").text();
        var name = $(event.target).parent().parent().parent().find("*:nth-child(2)").text();
        //If menu is being used by one or more languages, prevent deletion
        var numOfLang = $(event.target).parent().parent().parent().find(".language-flags").children().size();
        if (numOfLang > 0) {
            displayErrorDialog("Errore", "Impossibile eliminare un menu finch&egrave; &egrave; in uso da una lingua.");
            return;
        }
        $("#dcd-objname").text(name);
        $("#dcd-objid").val(id);
        $("#delete-confirm-dialog").dialog('open');
    });
    $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
        displayErrorDialog("Errore", "Errore: "+thrownError);
    });
    $("#dcd-ok").click(function(event) {
        var id = $("#dcd-objid").val();
        $("#delete-confirm-dialog").dialog("close");
        infos = {
            menuid: id
        };
        $.post('menuws.php?action=delete', infos, function (response) {
            if (response == "OK") {
                $(".idcell").each(function(index, element) {
                    if ($(element).text() == id) $(element).parent().remove();
                });
            } else {
                $("#error-dialog").find("#erd-errdata").val(response);
                $("#error-dialog").dialog("open");
            }
        });
    });
</script>

</body>
</html>

