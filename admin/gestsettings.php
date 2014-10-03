<?php
require_once("../bootstrap.php");
$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");

require_once("menu.php");
require_once("dialogs.php");

//TODO: Verify html session
?>
<!DOCTYPE HTML>
<html lang="it">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="favicon.gif" type="image/gif">
<title>Impostazioni Generali</title>

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
        <?php print_menu("Impostazioni"); ?>
    </div>

    <div id="main">
    	<div id="toolbar">
    		<h1>Impostazioni Generali</h1>	
    	</div>
        	<table class="pure-table pure-table-striped center" id="content-table" style="margin-top: 3em;">
        		<thead>
        		<tr style="font-size: 1.1em; text-align: center;">
        			<th>Chiave</th>
        			<th>Valore</th>
        			<th></th>
        		</tr>
        		</thead>
        		
        		<tbody>
        			<?php
                        $settings = $em->getRepository('Model\Setting')->findAll();
        				foreach ($settings as $setting) {
							?>
							<tr>
								<td class="setkey"><?php echo $setting->getSettingKey(); ?></td>
								<td class="setvalue"><?php echo $setting->getSettingValue(); ?></td>
								<td class="pure-form valeditor" style="display: none;"><input type="text" id="custombg" class="txteditor">
									<button type="button" class="pure-button openfileman">Sfoglia</button>
								</td>
								<td><i class="fa fa-pencil-square fa-2x btnmod"></i>
								<i class="fa fa-2x fa-check btnsave" style="display: none;"></i></td>
							</tr>
							<?php
						}
        			?>
        		</tbody>
        	</table>
    </div>
</div>

<div id="roxyCustomPanel" style="display: none;">
  <iframe src="" style="width:100%;height:100%" frameborder="0"></iframe>
</div>

<?php errorDialog(); ?>
<?php successfulOperationDialog(); ?>

<script src="js/menu.js"></script>
<script>
function CustomRoxyFileBrowser(field_name) {
  var roxyFileman = 'fileman/index.html';
  if (roxyFileman.indexOf("?") < 0) {     
    roxyFileman += "?input=" + field_name;   
  }
  else {
    roxyFileman += "&input=" + field_name;
  }
  roxyFileman += '&integration=custom&dialogid=roxyCustomPanel';
  console.log(roxyFileman);
  
  $('#roxyCustomPanel').children().first().attr("src",roxyFileman);
  $('#roxyCustomPanel').dialog({
  	modal:true,
  	width:875,
  	height:600,
  	title: 'File Manager'
  });
  
  return false; 
}
$('.openfileman').click(function(event) {
	CustomRoxyFileBrowser($(event.target).prev().attr('id'));
});
$('.btnmod').click(function(event) {
	var row = $(event.target).hide().parent().parent();
	var setvalue = row.find(".setvalue").hide().text();
	row.find(".valeditor").show();
	row.find(".txteditor").val(setvalue);
	row.find(".btnsave").show();
});
$('.btnsave').click(function(event) {
	var row = $(event.target).hide().parent().parent();
	var setkey = row.find(".setkey").text();
	var setvalue = row.find(".txteditor").val();
	row.find(".valeditor").hide();
	row.find(".setvalue").text(setvalue).show();
	row.find(".btnmod").show();
	
	//Save setvalue on db
	$.post('settingsws.php', {setkey: setkey, setvalue: setvalue}, function (response) {
		if (response == "OK") {
			//Show Operation complete dialog
			$("#successful-op-dialog").dialog("open");
		} else {
			//Show error dialog
			$("#erd-errdata").val(response);
			$("#error-dialog").dialog("open");
		}
	});
});
$(document).ajaxError(function (event, jqxhr, settings, thrownError) {
	//var cloned = $("#error-dialog").clone(true);
	var cloned = $("#error-dialog");
	cloned.find("p").remove();
	cloned.find("textarea").remove();
	cloned.children().first().before($("<p>Errore: "+thrownError+"</p>"));
	$(cloned).dialog("open");
});
</script>

</body>
</html>

