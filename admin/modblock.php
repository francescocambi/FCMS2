<?php
/**
 * User: francesco
 * Date: 10/9/14
 * Time: 11:23 AM
 */
require_once("menu.php");
require_once("dialogs.php");
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

$UPDATE_MODE = isset($_GET['blockid']);

if ($UPDATE_MODE) {
    try {
        $block = $em->find('Model\ContentBlock', $_GET['blockid']);
    } catch (Exception $e) {
        $EXCEPTION_THROWN = $e;
    }
}

function blockStyleComboBox($blockStyleCN = null) {
    $rows = array(
        0 => array( "CN" => "CenteredBlockStyle", "NAME" => "Centrato"),
        1 => array( "CN" => "ExtendedBlockStyle", "NAME" => "Esteso")
    );

    foreach ($rows as $row) {
        if (!is_null($blockStyleCN) && $row['CN'] == $blockStyleCN)
            echo '<option value="'.$row['CN'].'" selected>'.$row['NAME'].'</option>';
        else
            echo '<option value="'.$row['CN'].'">'.$row['NAME'].'</option>';
    }
}

/* Procedura salvataggio dati su db */
if (isset($_POST['name'])) {

    //If insert mode, creates new instance
    if (!$UPDATE_MODE) {
        $block = new \Model\ContentBlock();
    }

    //Update instance values
    $block->setName($_POST['name']);
    $block->setDescription($_POST['description']);
    $block->setBlockStyleClassName($_POST['blockStyleClassName']);
    $block->setBgurl($_POST['bgurl']);
    $block->setBgred($_POST['bgred']);
    $block->setBggreen($_POST['bggreen']);
    $block->setBgblue($_POST['bgblue']);
    $block->setBgopacity($_POST['bgopacity']);
    $block->setBgrepeatx(isset($_POST['bgrepeatx']));
    $block->setBgrepeaty(isset($_POST['bgrepeaty']));
    $block->setBgsize($_POST['bgsize']);
    if (strlen($_POST['content']) > 0)
        $block->setContent($_POST['content']);

    try {
        $em->beginTransaction();
        if ($UPDATE_MODE)
            $em->merge($block);
        else
            $em->persist($block);
        $em->flush();
        $em->commit();
        $OPERATION_COMPLETED = true;
    } catch (Exception $e) {
        $em->rollback();
        $EXCEPTION_THROWN = $e;
    }

}
?>
<!DOCTYPE HTML>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.gif" type="image/gif">
    <title>Editor Blocco</title>

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
        <?php print_menu("Gestione Blocchi"); ?>
</div>

<div id="main">
    <div id="toolbar">
        <h1 style="text-align: center; margin-top: 0; color: white;">Editor Blocco</h1>
    </div>

    <form method="POST" class="pure-form" id="block-editor-form">

        <div class="pure-form-aligned">
            <!-- DIV CON INFORMAZIONI BASE BLOCCO -->
            <div class="pure-u-1-2" style="min-width: 400px; float: left;">
                <legend>Info Base</legend>
                <?php
                if ($UPDATE_MODE) {
                ?>
                <div class="pure-control-group">
                    <label>Id</label>
                    <input type="text" id="id" value="<?php echo $block->getId(); ?>" disabled>
                </div>
                <?php } ?>
                <div class="pure-control-group">
                    <label>Nome</label>
                    <input type="text" id="blockname" name="name" value="<?php if ($UPDATE_MODE) echo $block->getName(); ?>">
                </div>
                <div class="pure-control-group">
                    <label>Descrizione</label>
                    <textarea type="text" name="description"><?php if ($UPDATE_MODE) echo $block->getDescription(); ?></textarea>
                </div>
                <div class="pure-control-group">
                    <label>Stile Blocco</label>
                    <select name="blockStyleClassName" size="1">
                        <?php
                        if ($UPDATE_MODE)
                            blockStyleComboBox($block->getBlockStyleClassName());
                        else
                            blockStyleComboBox();
                        ?>
                    </select>
                </div>
                <fieldset class="pure-control-group">
                    <label>  </label>
                    <button type="button" id="save-all" class="pure-button pure-button-primary" style="width: 200px;">Salva Tutto</button>
                </fieldset>
            </div>

            <!-- DIV CON INFORMAZIONI SFONDO BLOCCO -->
            <div class="pure-u-1-2" style="float: right; min-width: 500px;">
                <legend>Sfondo Blocco</legend>
                <div class="pure-control-group">
                    <label>URL Immagine</label>
                    <input type="text" id="bgurl" name="bgurl" value="<?php if ($UPDATE_MODE) echo $block->getBgurl(); ?>">
                    <button type="button" class="pure-button openfileman">Sfoglia</button>
                </div>

                <div class="pure-control-group">
                    <label>Colore</label>
                    <input type="text" name="bgred" size="3" placeholder="Red" value="<?php if ($UPDATE_MODE) echo $block->getBgred(); ?>">
                    <input type="text" name="bggreen" size="3" placeholder="Green" value="<?php if ($UPDATE_MODE) echo $block->getBggreen(); ?>">
                    <input type="text" name="bgblue" size="3" placeholder="Blue" value="<?php if ($UPDATE_MODE) echo $block->getBgblue(); ?>">
                    <input type="text" name="bgopacity" size="3" placeholder="Opacity" value="<?php if ($UPDATE_MODE) echo $block->getBgopacity(); ?>">
                </div>

                <div class="pure-control-group">
                    <label>Ripetizione Immagine</label>
                    <table class="pure-table pure-table-striped" style="display: inline-table;">
                        <tr><td><input type="checkbox" name="bgrepeatx" <?php if ($UPDATE_MODE && $block->getBgrepeatx()) echo 'checked'; ?>></td><td>Orizzontale</td></tr>
                        <tr><td><input type="checkbox" name="bgrepeaty" <?php if ($UPDATE_MODE && $block->getBgrepeaty()) echo 'checked'; ?>></td><td>Verticale </td></tr>
                    </table>
                </div>

                <div class="pure-control-group" style="clear: both;">
                    <label>Dimensioni</label>
                    <table class="pure-table pure-table-striped" style="display: inline-table;">
                        <tr><td><input type="radio" name="bgsize" value="cover" <?php if ($UPDATE_MODE && $block->getBgsize() == "cover") echo 'checked'; ?>></td><td>Esteso 100%</td></tr>
                        <tr><td><input type="radio" name="bgsize" value="contain" <?php if ($UPDATE_MODE && $block->getBgsize() == "contain") echo 'checked'; ?>></td><td>Esteso in proporzione</td></tr>
                        <tr><td><input type="radio" name="bgsize" value="" <?php if (!$UPDATE_MODE || ($UPDATE_MODE && $block->getBgsize() == "")) echo 'checked'; ?>></td><td>Dimensioni reali</td></tr>
                    </table>
                </div>
            </div>



        </div>

    <legend>Contenuto</legend>

    <!-- INIZIO AREA EDITING BLOCCO -->
        <div class="blockeditor" style="padding: 20px;">
            <?php if ($UPDATE_MODE) { ?>
                <div class="blockcontentdiv">
                    <?php echo $block->getContent(); //Assuming this is always a content block ?>
                </div>
                <textarea id="tmceeditor" class="blockcontent" name="content" style="display: none;"></textarea>
            <?php } else { ?>
                <textarea id="tmceeditor" class="blockcontent" name="content"></textarea>
            <?php } ?>
            <div class="blockbuttons" style="margin-top: 5px; clear: both;">
                <button type="button" class="pure-button pure-button-primary applyblock" style="display: none;">Applica Modifiche</button>
                <button type="button" class="pure-button pure-button-primary modblock">Modifica</button>
            </div>
        </div>
    <!-- FINE AREA EDITING BLOCCO -->

    </form>

    </div>
</div>

<div id="roxyCustomPanel" style="display: none;">
    <iframe src="" style="width:100%;height:100%" frameborder="0"></iframe>
</div>

<style>
    legend {
        margin-left: 10px;
    }
</style>

<script type="text/javascript" src="js/modblock.js"></script>

<script type="text/javascript">
    var UPDATE_MODE = <?php if ($UPDATE_MODE) echo "true"; else echo "false"; ?>;
    <?php if (!$UPDATE_MODE) { ?> $(".modblock").click(); <?php } ?>
</script>

<?php
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