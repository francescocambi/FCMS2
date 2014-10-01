<!DOCTYPE HTML>
<?php
require_once("menu.php");
require_once("dialogs.php");
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

//TODO: Verify html session

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

function blockStyleComboBox() {
    $rows = array(
        0 => array( "CN" => "CenteredBlockStyle", "NAME" => "Centrato"),
        1 => array( "CN" => "ExtendedBlockStyle", "NAME" => "Esteso")
    );
	
	foreach ($rows as $row) {
		echo '<option value="'.$row['CN'].'">'.$row['NAME'].'</option>';
	}
}

/**
 * @param $em Doctrine\ORM\EntityManager
 * @param $_POST array
 * @param null $pageid int
 */
function insertUpdateProcessing($em, $data, $pageid = null) {
    $update = !is_null($pageid);

    if ($update)
        $page = $em->find('Model\Page', $pageid);
    else
        $page = new Model\Page();

    if ($update) {
        $deleteurl = $em->createQueryBuilder()
            ->delete()
            ->from('Model\Url', "url")
            ->where("url.page=".$page->getId())
            ->getQuery();
        $deletepageblock = $em->createQueryBuilder()
            ->delete()
            ->from('Model\PageBlock', "pb")
            ->where("pb.page=".$page->getId())
            ->getQuery();
        $deleteurl->execute();
        $deletepageblock->execute();
    }

    $page->setName($data['name']);
    $page->setTitle($data['title']);
    $page->setPublished(!is_null($data['published']));
    $page->setPublic(!is_null($data['public']));
    $language = $em->find('Model\Language', $data['language']);
    $page->setLanguage($language);

    //Url insertion
    foreach ($data['url'] as $urlstring)
        if (strlen($urlstring) > 2) {
            $url = new \Model\Url();
            $url->setUrl($urlstring);
            $page->addUrl($url);
        }

    if (!$update) {
        $em->persist($page);
        $em->flush();
    }

    //Processing Blocks
    for ($i=0;$i<count($data['block']['id']);$i++) {
        $blockid = $data['block']['id'][$i];

        if ($blockid == 0) {
            //Insert new block
            $block = new \Model\ContentBlock();
        } else {
            //Update existing block
            $block = $em->find('Model\ContentBlock', $blockid);
        }

        //Sets block properties
        $block->setName($data['block']['name'][$i]);
        $block->setDescription($data['block']['description'][$i]);
        $block->setBlockStyleClassName($data['block']['style'][$i]); //TODO Require classname as select block style value
        $block->setBgurl($data['block']['bckurl'][$i]);
        $block->setBgred($data['block']['bckred'][$i]);
        $block->setBggreen($data['block']['bckgreen'][$i]);
        $block->setBgblue($data['block']['bckblue'][$i]);
        $block->setBgopacity($data['block']['bckopacity'][$i]);
        $block->setBgrepeatx($data['block']['bckrepeatx'][$i]);
        $block->setBgrepeaty($data['block']['bckrepeaty'][$i]);
        $block->setBgsize($data['block']['bcksize'][$i]);

        if (!is_null($data['block']['content'][$i]) && $data['block']['content'][$i] != "")
            $block->setContent($data['block']['content'][$i]);

        if ($blockid == 0)
            $em->persist($block);
        else
            $em->merge($block);

        $em->flush();

        //Adding block to page
        $page->addBlock($block, $i);
    }

    if ($update)
        $em->merge($page);
    else
        $em->persist($page);

    $em->flush();

}

/**
 * Page Insertion
 */
if (isset($_POST['name']) && !isset($_GET['pageid'])) {
    try {
        $em->beginTransaction();
        insertUpdateProcessing($em, $_POST);
        $em->commit();
		$OPERATION_COMPLETED = true;
	} catch (Exception $e) {
        $em->rollback();
		$EXCEPTION_THROWN = $e;
		echo "TRACE => ".$e->getTraceAsString();
		exit("\nEXCEPTION: ".$e->getMessage());
    }
}

/**
 * Modalità salvataggio dati in aggiornamento
 */
if (!is_null($_POST['name']) && !is_null($_GET['pageid'])) {
    try {
        $em->beginTransaction();
        insertUpdateProcessing($em, $_POST, $_GET['pageid']);
        $em->commit();
		$OPERATION_COMPLETED = true;
	} catch (Exception $e) {
        $em->rollback();
		$EXCEPTION_THROWN = $e;
		echo "TRACE => ".$e->getTraceAsString();
		exit("\nEXCEPTION: ".$e->getMessage());
	}
}

//Modalità modifica
$UPDATE_MODE = false;
$page = null;
if (!is_null($_GET['pageid']) && $_GET['pageid'] > 0) {
	//Retrieving all informations about this page
	try {
        $page = $em->find('Model\Page', $_GET['pageid']);
        $UPDATE_MODE = !is_null($page);
	} catch (Exception $e) {
		$EXCEPTION_THROWN = $e;
		// echo $e->getTraceAsString();
		// exit("\nEXCEPTION: ".$e->getMessage());
	}
}
?>
<!--suppress ALL -->
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
        <?php print_menu("Gestione Pagine"); ?>
    </div>

    <div id="main">
    	<div id="toolbar">
    		<h1 style="text-align: center; margin-top: 0; color: white;">Nuova Pagina</h1>
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
        			<input name="name" type="text" value="<?php (is_null($page) || print($page->getName())); ?>">
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Titolo</label>
        			<input name="title" type="text" value="<?php (is_null($page) || print($page->getTitle())); ?>">
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Pubblicata</label>
        			<input name="published" type="checkbox" <?php (is_null($page) || ($page->getPublished() && print("checked"))); ?>>
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Pubblica</label>
        			<input name="public" type="checkbox" <?php (is_null($page) || ($page->getPublic() && print("checked") )); ?>>&nbsp;&nbsp;
        			<button class="pure-button pure-button-primary" style="background: rgb(66, 184, 221);">Imposta gruppi abilitati</button> 
        		</div>
        		
        		<div class="pure-control-group">
        			<label>Lingua</label>
        			<select name="language">
        				<?php
                            $languages = $em->getRepository('Model\Language')->findAll();
							foreach ($languages as $l) {
								if (is_null($page) || $page->getLanguage()->equals($l))
									echo "<option value=\"".$l->getId()."\" selected>".$l->getDescription()."</option>";
								else
									echo "<option value=\"".$l->getId()."\">".$l->getDescription()."</option>";
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
                            foreach ($page->getPageUrls()->toArray() as $url) {
	        					?>
	        					<tr>
			        				<td mode="0"><?php echo $url->getUrl(); ?></td>
									<td><i class="fa fa-pencil-square fa-2x modurl"></i>&nbsp; &nbsp;<i class="fa fa-minus-square fa-2x delurl"></i></td>
									<input type="hidden" name="url[]" value="<?php echo $url->getUrl(); ?>">
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
                    $numBlocks = 0;
//                    var_dump($page->getPageBlocksArray());
//                    exit();
                    foreach ($page->getPageBlocksArray() as $pageBlock) {
                        $block = $pageBlock->getBlock();
                        ?>
        				<div class="blockeditor">
				        	<fieldset class="blockdata">
				        		<input type="hidden" name="block[id][]" value="<?php echo $block->getId(); ?>">
				    			<input type="hidden" name="block[name][]" value="<?php echo $block->getName() ?>">
				    			<input type="hidden" name="block[description][]" value="<?php echo $block->getDescription(); ?>">
				    			<input type="hidden" name="block[style][]" value="<?php echo $block->getBlockStyleClassName(); ?>">
				    			
				    			<input type="hidden" name="block[bckurl][]" value="<?php echo $block->getBgurl(); ?>">
				    			<input type="hidden" name="block[bckred][]" value="<?php echo $block->getBgred(); ?>">
				    			<input type="hidden" name="block[bckgreen][]" value="<?php echo $block->getBggreen(); ?>">
				    			<input type="hidden" name="block[bckblue][]" value="<?php echo $block->getBgblue(); ?>">
				    			<input type="hidden" name="block[bckopacity][]" value="<?php echo $block->getBgopacity(); ?>">
				    			<input type="hidden" name="block[bckrepeatx][]" value="<?php echo $block->getBgrepeatx(); ?>">
				    			<input type="hidden" name="block[bckrepeaty][]" value="<?php echo $block->getBgrepeaty(); ?>">
				    			<input type="hidden" name="block[bcksize][]" value="<?php echo $block->getBgsize(); ?>">
				        	</fieldset>
				        	<div class="blockcontentdiv">
				        		<?php echo $block->getContent(); //Assuming this is always a content block ?>
				        	</div>
				        	<textarea id="tmceeditor<?php echo $numBlocks++; ?>" class="blockcontent" name="block[content][]" style="display: none;"></textarea>
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
					<?php blockStyleComboBox(); ?>
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
			//Print block list
            $blocks = $em->getRepository('Model\Block')->findAll();
			
			foreach ($blocks as $block) {
				echo "<tr>";
				echo "<td><input class=\"nbm-blockcheck\" type=\"checkbox\" value=\"".$block->getId()."\"></td>";
				echo "<td>".$block->getName()."</td>";
				echo "<td>".$block->getBlockStyleClassName()."</td>"; //TODO Will print block style name not classname
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
					<?php blockStyleComboBox(); ?>
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
		editor_idx=<?php echo $numBlocks; ?>;
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