<!DOCTYPE HTML>
<?php
require_once("menu.php");
require_once("../bootstrap.php");
require_once("dialogs.php");
$em = initializeEntityManager("../");

//TODO: Verify html session
?>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="favicon.gif" type="image/gif">
<title>Gestione Pagine</title>

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
        <?php print_menu("Gestione Pagine"); ?>
    </div>

    <div id="main">
    	<div id="toolbar">
    		<h1>Gestione Pagine</h1>	
    	</div>
        	<table class="pure-table pure-table-striped center" id="content-table" style="margin-top: 3em;">
        		<thead>
        		<tr style="font-size: 1.1em;">
        			<th>ID</th>
        			<th>Nome</th>
        			<th>Titolo</th>
        			<th>Pubblicata</th>
        			<th></th>
        		</tr>
        		</thead>
        		
        		<tbody>
        		<?php
                    $pages = $em->getRepository('Model\Page')->findAll();
					foreach ($pages as $page) {
						$published = ($page->getPublished() == 1) ? "Sì" : "No";
						echo "<tr><td class=\"idcell\">".$page->getId()."</td><td>".$page->getName()."</td><td>".$page->getTitle()."</td><td>".$published."</td>
						<td><a href=\"newpag.php?pageid=".$page->getId()."\"><i class=\"fa  fa-pencil-square fa-lg\"></i></a><a href=\"#\" class=\"delpage\"><i class=\"fa fa-minus-square fa-lg\"></i></a></td></tr>";
					}
        		?>
        		<tr><td colspan="5" style="text-align: center;"><a href="newpag.php"><i class="fa fa-plus-square fa-2x"></i></a></td></tr>
        		</tbody>
        	</table>
    </div>
</div>

<?php deleteConfirmDialog(); ?>
<?php errorDialog(); ?>

<script src="js/menu.js"></script>
<script>
$(".delpage").click(function(event) {
	var id = $(event.target).parent().parent().parent().find("*:nth-child(1)").text();
	var name = $(event.target).parent().parent().parent().find("*:nth-child(2)").text();
	$("#dcd-objname").text(name);
	$("#dcd-objid").val(id);
	$("#delete-confirm-dialog").dialog('open');
});
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
	//var cloned = $("#error-dialog").clone(true);
	var cloned = $("#error-dialog");
	cloned.find("p").remove();
	cloned.find("textarea").remove();
	cloned.children().first().before($("<p>Errore: "+thrownError+"</p>"));
	$(cloned).dialog("open");
});
$("#dcd-ok").click(function(event) {
	var id = $("#dcd-objid").val();
	$("#delete-confirm-dialog").dialog("close");
	infos = {
		pageid: id
	};
	$.post('deletepage.php', infos, function (response) {
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

