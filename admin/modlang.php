<?php
/**
 * User: francesco
 * Date: 10/10/14
 * Time: 11:53 AM
 */

require_once("menu.php");
require_once("dialogs.php");
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

$UPDATE_MODE = isset($_GET['langid']);

if ($UPDATE_MODE)
    $language = $em->find('Model\Language', $_GET['langid']);

if (isset($_POST['code'])) {
    try {
        $em->beginTransaction();

        if (!$UPDATE_MODE)
            $language = new \Model\Language();

        $language->setCode($_POST['code']);
        $language->setDescription($_POST['description']);
        $language->setFlagImageURL($_POST['flagimageurl']);
        $menu = $em->find('Model\Menu', $_POST['menuid']);
        $language->setMenu($menu);

        if ($UPDATE_MODE)
            $em->merge($language);
        else
            $em->persist($language);

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
    <title>Editor Lingua</title>

    <link rel="stylesheet" href="css/pure-min.css">
    <link rel="stylesheet" href="css/forms-min.css">
    <link rel="stylesheet" href="css/font-awesome-4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/site.home.css">
    <script type="text/javascript" src="js/jquery-2.1.1.min.js"></script>
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
        <?php print_menu("Gestione Lingue"); ?>
    </div>

    <div id="main">
        <div id="toolbar">
            <h1 style="text-align: center; margin-top: 0; color: white;">Editor Lingua</h1>
        </div>

        <form method="POST" id="language-editor-form" class="pure-form pure-form-aligned">

            <legend style="margin-left: 10px;">Informazioni</legend>

            <?php if ($UPDATE_MODE) { ?>
                <div class="pure-control-group">
                    <label>Id</label>
                    <input type="text" value="<?php echo $language->getId(); ?>" id="id" disabled>
                </div>
            <?php } ?>
            <div class="pure-control-group">
                <label>Codice</label>
                <input type="text" name="code" maxlength="2" value="<?php if ($UPDATE_MODE) echo $language->getCode(); ?>">
            </div>
            <div class="pure-control-group">
                <label>Descrizione</label>
                <input type="text" name="description" value="<?php if ($UPDATE_MODE) echo $language->getDescription(); ?>">
            </div>
            <div class="pure-control-group">
                <label>Icona Bandierina</label>
                <input type="text" name="flagimageurl" id="flagimageurl" value="<?php if ($UPDATE_MODE) echo $language->getFlagImageURL(); ?>">
                <button type="button" class="pure-button pure-button-primary openfileman">Sfoglia</button>
            </div>
            <div class="pure-control-group">
                <label>Menu in lingua</label>
                <select size="1" name="menuid">
                    <?php
                    $menus = $em->getRepository('Model\Menu')->findAll();
                    foreach ($menus as $menu) {
                        if ($UPDATE_MODE && $language->getMenu()->getId()==$menu->getId())
                            echo "<option value=\"".$menu->getId()."\" selected>".$menu->getName()."</option>";
                        else
                            echo "<option value=\"".$menu->getId()."\">".$menu->getName()."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="pure-control-group">
                <label>  </label>
                <button type="button" class="pure-button pure-button-primary" id="save-all" style="width: 200px;">Salva Tutto</button>
            </div>

        </form>

    </div>

    <script type="text/javascript" src="js/modlang.js"></script>

    <div id="roxyCustomPanel" style="display: none;">
        <iframe src="" style="width:100%;height:100%" frameborder="0"></iframe>
    </div>

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