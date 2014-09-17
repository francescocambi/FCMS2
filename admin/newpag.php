<!DOCTYPE HTML>
<?php
require_once("menu.php");
require_once("../php/Connection.php");
require_once("dialogs.php");
$pdo = Connection::getPDO();

//TODO: Verify html session

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

function blockStyleComboBox($pdo) {
	$sql = "SELECT ID, NAME FROM BLOCK_STYLE";
	$statement = $pdo->prepare($sql);
	$statement->execute();
	
	foreach ($statement->fetchAll() as $row) {
		echo '<option value="'.$row['ID'].'">'.$row['NAME'].'</option>';
	}
}

if (isset($_POST['name']) && !isset($_GET['pageid'])) {
	//Modalità salvataggio dati in inserimento
	$sql = "INSERT INTO PAGE (NAME, TITLE, PUBLISHED, PUBLIC, LANGUAGE_ID) VALUES (?, ?, ?, ?, ?)";
	$sql2 = "INSERT INTO URL (URL, PAGE_ID) VALUES (?, ?)";
	$sql3 = "INSERT INTO BLOCK (NAME, DESCRIPTION, BLOCK_STYLE_ID, BG_URL, BG_RED, BG_GREEN, BG_BLUE, BG_OPACITY, BG_REPEATX, BG_REPEATY, BG_SIZE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$sql4 = "INSERT INTO BLOCK_CONTENT VALUES (?, ?)";
	$sql5 = "UPDATE BLOCK SET NAME=?, DESCRIPTION=?, BLOCK_STYLE_ID=?, BG_URL=?, BG_RED=?, BG_GREEN=?,
			 BG_BLUE=?, BG_OPACITY=?, BG_REPEATX=?, BG_REPEATY=?, BG_SIZE=? WHERE ID=?";
	$sql6 = "UPDATE BLOCK_CONTENT SET CONTENT=? WHERE ID=?";
	$sql7 = "INSERT INTO PAGE_BLOCK VALUES (?, ?, ?)";
	
	try {
		$pdo->beginTransaction();
		//Page Insertion
		$statement = $pdo->prepare($sql);
		$published = isset($_POST['published']);
		$public = isset($_POST['public']);
		$statement->execute(array($_POST['name'], $_POST['title'], $published, $public, $_POST['language']));
		//Retrieving page id
		$pageid = $pdo->lastInsertId();
		//Url Insertion
		$statement = $pdo->prepare($sql2);
		foreach ($_POST['url'] as $url)
			if ($url != "")
				$statement->execute(array($url, $pageid));
		//Processing Blocks
		for ($i=0;$i<count($_POST['block']['id']);$i++) {
			$blockid = $_POST['block']['id'][$i];
			if ($blockid == 0) {
				//Block Insertion
				$statement = $pdo->prepare($sql3);
				$statement->execute(array($_POST['block']['name'][$i], 
										  $_POST['block']['description'][$i],
										  $_POST['block']['style'][$i],
										  $_POST['block']['bckurl'][$i],
										  $_POST['block']['bckred'][$i],
										  $_POST['block']['bckgreen'][$i],
										  $_POST['block']['bckblue'][$i],
										  $_POST['block']['bckopacity'][$i],
										  $_POST['block']['bckrepeatx'][$i],
										  $_POST['block']['bckrepeaty'][$i],
										  $_POST['block']['bcksize'][$i]
										  ));
				$blockid = $pdo->lastInsertId();
				$statement = $pdo->prepare($sql4);
				$statement->execute(array($blockid, $_POST['block']['content'][$i]));
			} else if ($_POST['block']['content'][$i] != "") {
				//Block Update
				//Update block properties
				$statement = $pdo->prepare($sql5);
				$statement->execute(array($_POST['block']['name'][$i], 
										  $_POST['block']['description'][$i],
										  $_POST['block']['style'][$i],
										  $_POST['block']['bckurl'][$i],
										  $_POST['block']['bckred'][$i],
										  $_POST['block']['bckgreen'][$i],
										  $_POST['block']['bckblue'][$i],
										  $_POST['block']['bckopacity'][$i],
										  $_POST['block']['bckrepeatx'][$i],
										  $_POST['block']['bckrepeaty'][$i],
										  $_POST['block']['bcksize'][$i],
										  $blockid						  ));
				//Update block content
				$statement = $pdo->prepare($sql6);
				$statement->execute(array($_POST['block']['content'][$i], $blockid));
			}
			//Adding block to page
			$statement = $pdo->prepare($sql7);
			$statement->execute(array($i, $blockid, $pageid));
		}
		$pdo->commit();
		$OPERATION_COMPLETED = true;
	} catch (PDOException $e) {
		$pdo->rollback();
		$EXCEPTION_THROWN = $e;
		// echo "TRACE => ".$e->getTraceAsString();
		// exit("\nEXCEPTION: ".$e->getMessage());
	}
	
}

//Modalità salvataggio dati in aggiornamento
if (isset($_POST['name']) && isset($_GET['pageid'])) {
	//Preparazione query
	$sql1 = "UPDATE PAGE SET NAME=?, TITLE=?, PUBLISHED=?, PUBLIC=?, LANGUAGE_ID=? WHERE ID=?";
	$sql2 = "DELETE FROM URL WHERE PAGE_ID=?";
	$sql3 = "INSERT INTO URL (URL, PAGE_ID) VALUES (?, ?)";
	$sql4 = "DELETE FROM PAGE_BLOCK WHERE PAGE_ID=?";
	$sql5 = "INSERT INTO BLOCK (NAME, DESCRIPTION, BLOCK_STYLE_ID, BG_URL, BG_RED, BG_GREEN, BG_BLUE, BG_OPACITY,
			 BG_REPEATX, BG_REPEATY, BG_SIZE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
	$sql6 = "UPDATE BLOCK SET NAME=?, DESCRIPTION=?, BLOCK_STYLE_ID=?, BG_URL=?, BG_RED=?, BG_GREEN=?,
			 BG_BLUE=?, BG_OPACITY=?, BG_REPEATX=?, BG_REPEATY=?, BG_SIZE=? WHERE ID=?";
	$sql7 = "INSERT INTO BLOCK_CONTENT VALUES (?, ?)";
	$sql8 = "UPDATE BLOCK_CONTENT SET CONTENT=? WHERE ID=?";
	$sql9 = "INSERT INTO PAGE_BLOCK VALUES (?, ?, ?)";
	
	//Prepare $sql6 query for updating blocks
	$update_assoc = array( 
					array( "NAME", $_POST['block']['description'] ), 
					array( "DESCRIPTION", $_POST['block']['description'] ),
					array( "BLOCK_STYLE_ID", $_POST['block']['style'] ),
					array( "BG_URL", $_POST['block']['bckurl'] ),
					array( "BG_RED", $_POST['block']['bckred'] ),
					array( "BG_GREEN", $_POST['block']['bckgreen'] ),
					array( "BG_BLUE", $_POST['block']['bckblue'] ),
					array( "BG_OPACITY", $_POST['block']['bckopacity'] ),
					array( "BG_REPEATX", $_POST['block']['bckrepeatx'] ),
					array( "BG_REPEATY", $_POST['block']['bckrepeaty'] ),
					array( "BG_SIZE", $_POST['block']['bcksize'] )		);
	
	
	//Begin update
	try {
		$pdo->beginTransaction();
		//Page Insertion
		$statement = $pdo->prepare($sql1);
		$published = isset($_POST['published']);
		$public = isset($_POST['public']);
		$statement->execute(array($_POST['name'], $_POST['title'], $published, $public, $_POST['language'], $_GET['pageid']));
		//Url reset
		$statement = $pdo->prepare($sql2);
		$statement->execute( array($_GET['pageid']) );
		//Url Insertion
		$statement = $pdo->prepare($sql3);
		foreach ($_POST['url'] as $url)
			if ($url != "")
				$statement->execute(array($url, $_GET['pageid']));
		//Page Block reset
		$statement = $pdo->prepare($sql4);
		$statement->execute(array($_GET['pageid']));
		//Processing Blocks
		for ($i=0;$i<count($_POST['block']['id']);$i++) {
			$blockid = $_POST['block']['id'][$i];
			if ($blockid == 0) {
				//Block Insertion
				$statement = $pdo->prepare($sql5);
				$statement->execute(array($_POST['block']['name'][$i],
										  $_POST['block']['description'][$i],
										  $_POST['block']['style'][$i],
										  $_POST['block']['bckurl'][$i],
										  $_POST['block']['bckred'][$i],
										  $_POST['block']['bckgreen'][$i],
										  $_POST['block']['bckblue'][$i],
										  $_POST['block']['bckopacity'][$i],
										  $_POST['block']['bckrepeatx'][$i],
										  $_POST['block']['bckrepeaty'][$i],
										  $_POST['block']['bcksize'][$i]		  ));
				$blockid = $pdo->lastInsertId();
				$statement = $pdo->prepare($sql7);
				$statement->execute(array($blockid, $_POST['block']['content'][$i]));
			} else {
				//Block Update
				//Update Block Info
				$statement = $pdo->prepare($sql6);
				$statement->execute(array($_POST['block']['name'][$i],
										  $_POST['block']['description'][$i],
										  $_POST['block']['style'][$i],
										  $_POST['block']['bckurl'][$i],
										  $_POST['block']['bckred'][$i],
										  $_POST['block']['bckgreen'][$i],
										  $_POST['block']['bckblue'][$i],
										  $_POST['block']['bckopacity'][$i],
										  $_POST['block']['bckrepeatx'][$i],
										  $_POST['block']['bckrepeaty'][$i],
										  $_POST['block']['bcksize'][$i],
										  $blockid ));
				if ($_POST['block']['content'][$i] != "") {
					//Update Block Content Info
					$statement = $pdo->prepare($sql8);
					$statement->execute(array($_POST['block']['content'][$i], $blockid));
				}
			}
			//Adding block to page
			$statement = $pdo->prepare($sql9);
			$statement->execute(array($i, $blockid, $_GET['pageid']));
		}
		$pdo->commit();
		$OPERATION_COMPLETED = true;
	} catch (PDOException $e) {
		$pdo->rollback();
		$EXCEPTION_THROWN = $e;
		// echo "TRACE => ".$e->getTraceAsString();
		// exit("\nEXCEPTION: ".$e->getMessage());
	}
}

//Modalità modifica
$UPDATE_MODE = isset($_GET['pageid']) && $_GET['pageid'] != 0;
if ($UPDATE_MODE) {
	//Retrieving all informations about this page
	$pagedata = array();
	//Query Definition
	$sql1 = "SELECT ID, NAME, TITLE, PUBLISHED, PUBLIC, LANGUAGE_ID FROM PAGE WHERE ID=?";
	$sql2 = "SELECT URL FROM URL WHERE PAGE_ID=?";
	$sql3 = "SELECT BLOCK_CONTENT.ID, BLOCK_CONTENT.CONTENT, BLOCK.NAME, BLOCK.DESCRIPTION, BLOCK.BLOCK_STYLE_ID, BG_URL,
			 BG_RED, BG_GREEN, BG_BLUE, BG_OPACITY, BG_REPEATX, BG_REPEATY, BG_SIZE
			 FROM BLOCK NATURAL JOIN BLOCK_CONTENT JOIN PAGE_BLOCK ON BLOCK_ID=BLOCK_CONTENT.ID WHERE PAGE_ID=? ORDER BY VIEWORDER";
	try {
		//Retrieving Page Data
		$statement = $pdo->prepare($sql1);
		$statement->execute(array($_GET['pageid']));
		$row = $statement->fetch();
		//Collecting data in array pagedata
		$pagedata['id'] = $_GET['pageid'];
		$pagedata['name'] = $row['NAME'];
		$pagedata['title'] = $row['TITLE'];
		$pagedata['published'] = $row['PUBLISHED']==1;
		$pagedata['public'] = $row['PUBLIC']==1;
		$pagedata['language_id'] = $row['LANGUAGE_ID'];
		//Retrieving URL Data
		$statement = $pdo->prepare($sql2);
		$statement->execute(array($_GET['pageid']));
		$pagedata['url'] = array();
		foreach ($statement->fetchAll() as $row) {
			//Collecting data in array pagedata
			array_push($pagedata['url'], $row['URL']);
		}
		//Retrieving Block Data
		$statement = $pdo->prepare($sql3);
		$statement->execute(array($_GET['pageid']));
		$pagedata['block']['id'] = array();
		$pagedata['block']['content'] = array();
		$pagedata['block']['name'] = array();
		$pagedata['block']['description'] = array();
		$pagedata['block']['style'] = array();
		$pagedata['block']['bckurl'] = array();
		$pagedata['block']['bckred'] = array();
		$pagedata['block']['bckgreen'] = array();
		$pagedata['block']['bckblue'] = array();
		$pagedata['block']['bckopacity'] = array();
		$pagedata['block']['bckrepeatx'] = array();
		$pagedata['block']['bckrepeaty'] = array();
		$pagedata['block']['bcksize'] = array();
		foreach ($statement->fetchAll() as $row) {
			//Collecting data in array pagedata
			array_push($pagedata['block']['id'], $row['ID']);
			array_push($pagedata['block']['content'], $row['CONTENT']);
			array_push($pagedata['block']['name'], $row['NAME']);
			array_push($pagedata['block']['description'], $row['DESCRIPTION']);
			array_push($pagedata['block']['style'], $row['BLOCK_STYLE_ID']);
			array_push($pagedata['block']['bckurl'], $row['BG_URL']);
			array_push($pagedata['block']['bckred'], $row['BG_RED']);
			array_push($pagedata['block']['bckgreen'], $row['BG_GREEN']);
			array_push($pagedata['block']['bckblue'], $row['BG_BLUE']);
			array_push($pagedata['block']['bckopacity'], $row['BG_OPACITY']);
			array_push($pagedata['block']['bckrepeatx'], $row['BG_REPEATX']);
			array_push($pagedata['block']['bckrepeaty'], $row['BG_REPEATY']);
			array_push($pagedata['block']['bcksize'], $row['BG_SIZE']);
		}
	} catch (PDOException $e) {
		$EXCEPTION_THROWN = $e;
		// echo $e->getTraceAsString();
		// exit("\nEXCEPTION: ".$e->getMessage());
	}
}
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
	<style>

	</style>
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
    		<h1 style="text-align: center; margin-top: 0px; color: white;">Nuova Pagina</h1>	
    	</div>
    	<form class="pure-form pure-form-aligned" method="POST">
    	
        	<!-- <div style="width: 550px; height: 350px; padding: 15px; float: left;">   	 -->
        	<div class="pure-u-12-24" style="padding: 10px;">
        		<legend>Pagina</legend>
        		
        		<?php if ($UPDATE_MODE) { ?>
	        		<div class="pure-control-group">
	        			<label>ID</label>
	        			<input id="id" type="text" value="<?php print($_GET['pageid']); ?>" disabled>
	        		</div>
	        	<?php } ?>
        		
        		<div class="pure-control-group">
        			<label>Nome</label>
        			<input name="name" type="text" value="<?php (isset($pagedata) && print($pagedata['name'])); ?>">
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Titolo</label>
        			<input name="title" type="text" value="<?php (isset($pagedata) && print($pagedata['title'])); ?>">
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Pubblicata</label>
        			<input name="published" type="checkbox" <?php (isset($pagedata) && $pagedata['published'] && print("checked")); ?>>
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Pubblica</label>
        			<input name="public" type="checkbox" <?php (isset($pagedata) && $pagedata['public'] && print("checked")); ?>>&nbsp;&nbsp;
        			<button class="pure-button pure-button-primary" style="background: rgb(66, 184, 221);">Imposta gruppi abilitati</button> 
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Lingua</label>
        			<select name="language">
        				<?php
        					$sql = "SELECT ID, DESCRIPTION FROM LANGUAGES";
							$statement = $pdo->prepare($sql);
							$statement->execute();
							foreach ($statement->fetchAll() as $row) {
								if (isset($pagedata) && $row['ID']==$pagedata['language_id'])
									echo "<option value=\"".$row['ID']."\" selected>".$row['DESCRIPTION']."</option>";
								else
									echo "<option value=\"".$row['ID']."\">".$row['DESCRIPTION']."</option>";
							}
						?>
        			</select>
        		</div>
        		<button type="submit" class="pure-button pure-button-primary">Salva tutto</button>
        	</div>
        	
        	<!-- <div style="width: 500px; height: 320px; padding: 15px; float: right; overflow: scroll;"> -->
        	<div class="pure-u-10-24" style="overflow: scroll; padding: 10px; float: right;">
        		<legend>Url Pagina</legend>
        		<div class="pure-control-group">
	        		<table class="pure-table pure-table-striped center" style="text-align: center;">
	        			<thead>
	        				<th style="width: 200px; text-align: center;">http://www.*****.it/</th>
	        				<th></th>
	        			</thead>
	        			<?php
	        			if ($UPDATE_MODE) {
	        				foreach ($pagedata['url'] as $url) {
	        					?>
	        					<tr>
			        				<td mode="0"><?php echo $url; ?></td>
									<td><i class="fa fa-pencil-square fa-2x modurl"></i>&nbsp; &nbsp;<i class="fa fa-minus-square fa-2x delurl"></i></td>
									<input type="hidden" name="url[]" value="<?php echo $url; ?>">	
								</tr>
	        					<?php
	        				}
	        			}
						?>
	        			<tr id="newurlrow">
	        				<td colspan="2"><button type="button" class="pure-button" id="newurl">Nuovo</button></td>
	        			</tr>
	        			<tr style="display: none;" id="urlrowtpl">
	        				<td mode="1"><input type="text"></td>
							<td><i class="fa fa-pencil-square fa-2x modurl" style="color: #0078e7;"></i>&nbsp; &nbsp;<i class="fa fa-minus-square fa-2x delurl" style="color: #0078e7;"></i></td>
							<input type="hidden" name="url[]">	
						</tr>
	        		</table>
	        	</div>
        		
        	</div>
        	
        	<legend style="line-height: 40px; margin: 5px; width: 99%">Blocchi Pagina <button type="button" class="pure-button pure-button-primary" style="float: right;" id="newblock">Aggiungi Blocco</button></legend>
        	<div id="blocks" style="padding: 20px;">
        		<?php
        		if ($UPDATE_MODE) {
        			for ($i=0;$i<count($pagedata['block']['id']);$i++) {
        				?>
        				<div class="blockeditor">
				        	<fieldset class="blockdata">
				        		<input type="hidden" name="block[id][]" value="<?php echo $pagedata['block']['id'][$i]; ?>">
				    			<input type="hidden" name="block[name][]" value="<?php echo $pagedata['block']['name'][$i]; ?>">
				    			<input type="hidden" name="block[description][]" value="<?php echo $pagedata['block']['description'][$i]; ?>">
				    			<input type="hidden" name="block[style][]" value="<?php echo $pagedata['block']['style'][$i]; ?>">
				    			
				    			<input type="hidden" name="block[bckurl][]" value="<?php echo $pagedata['block']['bckurl'][$i]; ?>">
				    			<input type="hidden" name="block[bckred][]" value="<?php echo $pagedata['block']['bckred'][$i]; ?>">
				    			<input type="hidden" name="block[bckgreen][]" value="<?php echo $pagedata['block']['bckgreen'][$i]; ?>">
				    			<input type="hidden" name="block[bckblue][]" value="<?php echo $pagedata['block']['bckblue'][$i]; ?>">
				    			<input type="hidden" name="block[bckopacity][]" value="<?php echo $pagedata['block']['bckopacity'][$i]; ?>">
				    			<input type="hidden" name="block[bckrepeatx][]" value="<?php echo $pagedata['block']['bckrepeatx'][$i]; ?>">
				    			<input type="hidden" name="block[bckrepeaty][]" value="<?php echo $pagedata['block']['bckrepeaty'][$i]; ?>">
				    			<input type="hidden" name="block[bcksize][]" value="<?php echo $pagedata['block']['bcksize'][$i]; ?>">
				        	</fieldset>
				        	<div class="blockcontentdiv">
				        		<?php echo $pagedata['block']['content'][$i]; ?>
				        	</div>
				        	<textarea id="tmceeditor<?php echo $i; ?>" class="blockcontent" name="block[content][]" style="display: none;"></textarea>
				        	<div class="blockbuttons" style="margin-top: 5px; clear: both;">
				        		<button type="button" class="pure-button pure-button-primary upbutton">Su</button>
					    		<button type="button" class="pure-button pure-button-primary downbutton">Giu</button>
					    		<button type="button" class="pure-button pure-button-primary delblock">Elimina</button>
					    		&nbsp;&nbsp;&nbsp;&nbsp;
					    		<button type="button" class="pure-button pure-button-primary applyblock" style="display: none;">Applica Modifiche</button>
					    		<button type="button" class="pure-button pure-button-primary modblock">Modifica</button>
					    		&nbsp;&nbsp;&nbsp;&nbsp;
					    		<button type="button" class="pure-button pure-button-primary blockproperties">Propriet&agrave; Blocco</button>
				        	</div>
				        </div> 
        				<?php
        			}
        		}
        		?>
	    	</div>
	        
	        </form>
	        
	        <div id="editormodel" class="blockeditor" style="display: none;">
	        	<fieldset class="blockdata">
	        		<input type="hidden" name="block[id][]" value="0">
	    			<input type="hidden" name="block[name][]">
	    			<input type="hidden" name="block[description][]">
	    			<input type="hidden" name="block[style][]">
	    			
	    			<input type="hidden" name="block[bckurl][]">
	    			<input type="hidden" name="block[bckred][]">
	    			<input type="hidden" name="block[bckgreen][]">
	    			<input type="hidden" name="block[bckblue][]">
	    			<input type="hidden" name="block[bckopacity][]">
	    			<input type="hidden" name="block[bckrepeatx][]">
	    			<input type="hidden" name="block[bckrepeaty][]">
	    			<input type="hidden" name="block[bcksize][]">
	        	</fieldset>
	        	<textarea class="blockcontent" name="block[content][]" style="display: none;"></textarea>
	        	<div class="blockbuttons" style="margin-top: 5px; clear: both;">
	        		<button type="button" class="pure-button pure-button-primary upbutton">Su</button>
		    		<button type="button" class="pure-button pure-button-primary downbutton">Giu</button>
		    		<button type="button" class="pure-button pure-button-primary delblock">Elimina</button>
		    		&nbsp;&nbsp;&nbsp;&nbsp;
		    		<button type="button" class="pure-button pure-button-primary applyblock">Applica Modifiche</button>
		    		<button type="button" class="pure-button pure-button-primary modblock">Modifica</button>
		    		&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="button" class="pure-button pure-button-primary blockproperties">Propriet&agrave; Blocco</button>
	        	</div>
	        </div> 
	        
		</div>
    </div>
</div>

<!-- INIZIO CODICE DEFINIZIONE DIALOG NUOVO BLOCCO -->
<div id="newblockmodal" style="height: 400px; width: 600px; font-size: 0.9em;">
	<fieldset class="pure-form" style="text-align: center; margin-bottom: 10px;" id="nbm-tabcontrol">
		<button type="button" id="nbm-new" class="pure-button pure-button-primary" onclick="nbmtabcontrol(this)">Nuovo Blocco</button>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<button type="button" id="nbm-exist" class="pure-button" onclick="nbmtabcontrol(this)">Blocco Esistente</button>
	</fieldset> 
	<div id="nbm-newblockscreen" class="pure-form pure-form-aligned">
		<form action="#" id="nbm-newblockform">
		<fieldset>
			<div class="pure-control-group">
				<label>Nome</label>
				<input type="text" id="nbm-name">
			</div>
			<div class="pure-control-group">
				<label>Descrizione</label>
				<textarea id="nbm-description"></textarea>
			</div>
			<div class="pure-control-group">
				<label>Stile Blocco</label>
				<select id="nbm-style">
					<?php blockStyleComboBox($pdo); ?>
				</select>
			</div>
		</fieldset>
		</form>
		<button type="button" id="nbm-addnew" class="pure-button pure-button-primary" style="float: right;">Aggiungi</button>
	</div>
	<div id="nbm-insertblockscreen" style="display: none;">
		<form action="#" id="nbm-insertblockform">
		<div style="max-height: 350px; overflow: scroll;">
		<table class="pure-table pure-table-striped center">
			<thead>
				<tr>
					<th></th>
					<th>Nome</th>
					<th>Stile</th>
				</tr>
			</thead>
			<?php
			//Retrieve blocks
			$sql = "SELECT BLOCK.ID, BLOCK.NAME, BLOCK_STYLE.NAME 'STYLE'
					FROM BLOCK, BLOCK_STYLE
					WHERE BLOCK.BLOCK_STYLE_ID = BLOCK_STYLE.ID";
			
			$statement = $pdo->prepare($sql);
			$statement->execute();
			
			foreach ($statement->fetchAll() as $row) {
				echo "<tr>";
				echo "<td><input class=\"nbm-blockcheck\" type=\"checkbox\" value=\"".$row['ID']."\"></td>";
				echo "<td>".$row['NAME']."</td>";
				echo "<td>".$row['STYLE']."</td>";
				echo "</tr>";
			}
			
			?>
		</table>
		</div>
		<button type="button" id="nbm-addexist" class="pure-button pure-button-primary" style="float: right;">Aggiungi</button>
		</form>
		
	</div>
</div>
<!-- FINE CODICE DEFINIZIONE DIALOG NUOVO BLOCCO -->

<!-- INIZIO CODICE DEFINIZIONE DIALOG PROPRIETA BLOCCO --> 
<div id="blockpropdialog" style="font-size: 0.9em;">
	<form action="#" class="pure-form pure-form-aligned" id="bpd-form">
		<fieldset>

			<legend>Propriet&agrave; Blocco</legend>
			
			<div class="pure-control-group">
				<label>Nome</label>
				<input type="text" id="bpd-blockname">
			</div>
			
			<div class="pure-control-group">
				<label>Descrizione</label>
				<textarea id="bpd-blockdescription"></textarea>
			</div>
			
			<div class="pure-control-group">
				<label>Stile Blocco</label>
				<select id="bpd-blockstyle">
					<?php blockStyleComboBox($pdo); ?>
				</select>
			</div><br>
			
			<legend>Sfondo Blocco</legend>
			
			<div class="pure-control-group">
				<label>URL Immagine</label>
				<input type="text" id="bpd-txtbckurl">
				<button type="button" class="pure-button openfileman">Sfoglia</button>
			</div>
			
			<div class="pure-control-group">
				<label>Colore</label>
				<input type="text" id="bpd-txtbckred" size="3" placeholder="Red">
				<input type="text" id="bpd-txtbckgreen" size="3" placeholder="Green">
				<input type="text" id="bpd-txtbckblue" size="3" placeholder="Blue">
				<input type="text" id="bpd-txtbckopacity" size="3" placeholder="Opacity">
			</div>
			
			<div class="pure-control-group">
				<label>Ripetizione Immagine</label>
				<table class="pure-table pure-table-striped" style="display: inline-table;">
					<tr><td><input type="checkbox" id="bpd-chkbckrepeatx"></td><td>Orizzontale</td></tr>
					<tr><td><input type="checkbox" id="bpd-chkbckrepeaty"></td><td>Verticale </td></tr>
				</table>
			</div>
			
			<div class="pure-control-group" style="clear: both;">
				<label>Dimensioni</label>
				<table class="pure-table pure-table-striped" style="display: inline-table;">
					<tr><td><input type="radio" id="bpd-bcksizecover" name="bpd-bcksize" value="cover"></td><td>Esteso 100%</td></tr>
					<tr><td><input type="radio" id="bpd-bcksizecontain" name="bpd-bcksize" value="contain"></td><td>Esteso in proporzione</td></tr>
					<tr><td><input type="radio" id="bpd-bcksizeinit" name="bpd-bcksize" value="" checked></td><td>Dimensioni reali</td></tr>
				</table>
			</div>
		</fieldset>
		<button type="button" class="pure-button pure-button-primary" id="bpd-btnsave" style="float: right;">Salva</button>
	</form>
</div>
<!-- FINE CODICE DEFINIZIONE DIALOG PROPRIETA BLOCCO -->


<div id="roxyCustomPanel" style="display: none;">
  <iframe src="" style="width:100%;height:100%" frameborder="0"></iframe>
</div>

<script type="text/javascript" src="js/form.actions.js"></script>
<script src="js/menu.js"></script>
<?php
if ($UPDATE_MODE) {
	?>
	<script type="text/javascript">
		editor_idx=<?php echo count($pagedata['block']['id']); ?>;
	</script>
	<?php
}
if ($OPERATION_COMPLETED) {
	showSuccessfulOperationDialog();
}
if ($EXCEPTION_THROWN != null) {
	showErrorDialog($EXCEPTION_THROWN->getMessage(), $EXCEPTION_THROWN->getTraceAsString());
} else {
	errorDialog();
}
?>
</body>
</html>