<?php
require_once("menu.php");
require_once("dialogs.php");
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionRedirect.php");

$EXCEPTION_THROWN = null;
$OPERATION_COMPLETED = false;

$UPDATE_MODE = isset($_GET['menuid']);

if ($UPDATE_MODE)
    $menu = $em->find('Model\Menu', $_GET['menuid']);

if (isset($_POST['label'])) {
    try {

        $em->beginTransaction();

        if (!$UPDATE_MODE)
            $menu = new \Model\Menu();

        //If update mode delete all items on db
        if ($UPDATE_MODE)
            foreach ($menu->getChildren() as $child)
                $em->remove($child);

        $menu->setName($_POST['menu_name']);
        $menu->setDescription($_POST['menu_description']);

        if ($UPDATE_MODE)
            $em->merge($menu);
        else
            $em->persist($menu);
        $em->flush();
        $items = insertMenu($_POST, $menu->getId(), array());
        foreach ($items as $item) {
            $menu->addMenuItem($item);
        }
        $em->merge($menu);
        $em->flush();
        $em->commit();
        $OPERATION_COMPLETED = true;
    } catch (Exception $e) {
        $em->rollback();
        $EXCEPTION_THROWN = $e;
    }
}

function insertMenu($data, $menuid, $order) {
    $menuitems = array();
    for ($i = 0; $i < count($data['level']); $i++) {
        if (strlen($data['label'][$i]) > 0) {
            $menuitem = new \Model\MenuItem();
            $menuitem->setLabel($data['label'][$i]);
            $menuitem->setUrl($data['url'][$i]);
            if (!isset($order[$data['level'][$i]])) $order[$data['level'][$i]] = 0;
            $menuitem->setItemOrder($order[$data['level'][$i]]++);
            $menuitem->setMenu($menuid);
            $menuitem->setHidden(false);
            //Se questo elemento ha figli
            if ($data['level'][$i+1] != $data['level'][$i]) {
                $processedItems = processChildren($data, $i, $menuid, $order, $data['level'][$i+1]);
                $i += count($processedItems);
                foreach ($processedItems as $item) {
                    $menuitem->addChild($item);
                }
            }
            array_push($menuitems, $menuitem);
        }
    }
    return $menuitems;
}

//Processa figli, ritorna un array contenente i menu item figli
function processChildren($data, $i, $menuid, $order, $level) {
    $menuitems = array();
    for ($i++; $i < count($data['level']) && $data['level'][$i] == $level; $i++) {
        if (strlen($data['label'][$i]) > 0) {
            $menuitem = new \Model\MenuItem();
            $menuitem->setLabel($data['label'][$i]);
            $menuitem->setUrl($data['url'][$i]);
            if (!isset($order[$data['level'][$i]])) $order[$data['level'][$i]] = 0;
            $menuitem->setItemOrder($order[$data['level'][$i]]++);
            $menuitem->setMenu($menuid);
            $menuitem->setHidden(false);
            //Se questo elemento ha figli
            if ($data['level'][$i+1] > $data['level'][$i]) {
                $processedItems = processChildren($data, $i, $menuid, $order, $data['level'][$i+1]);
                $i += count($processedItems);
                foreach ($processedItems as $item)
                    $menuitem->addChild($item);
            }
            array_push($menuitems, $menuitem);
        }
    }
    return $menuitems;
}

?>
<!DOCTYPE HTML>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.gif" type="image/gif">
    <title>Editor Menu</title>

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
            <h1 style="text-align: center; margin-top: 0; color: white;">Editor Menu</h1>
        </div>

        <form method="POST" class="pure-form">

            <div class="pure-form-aligned">
                <legend>Informazioni</legend>
                <?php
                if ($UPDATE_MODE) {
                    ?>
                    <div class="pure-control-group">
                        <label>Id</label>
                        <input type="text" value="<?php echo $menu->getId(); ?>" disabled>
                    </div>
                    <?php
                }
                ?>
                <div class="pure-control-group">
                    <label>Name</label>
                    <input type="text" name="menu_name" value="<?php if ($UPDATE_MODE) echo $menu->getName(); ?>">
                </div>
                <div class="pure-control-group">
                    <label>Description</label>
                    <textarea type="text" name="menu_description" value="<?php if ($UPDATE_MODE) echo $menu->getDescription(); ?>"></textarea>
                </div>
                <div class="pure-control-group">
                    <label>  </label>
                    <button type="submit" class="pure-button pure-button-primary" style="width: 200px;">Salva Tutto</button>
                </div>
            </div>

            <legend>Struttura</legend>
        <!-- INIZIO AREA EDITING MENU -->
            <ul>

                <?php
                if ($UPDATE_MODE) {
                    $menu = $em->find('Model\Menu', $_GET['menuid']);
                    echo generateFor($menu, 0);
                }

                function generateFor(Model\IHierarchicalMenu $menu, $level) {
                    $children = $menu->getChildren();
                    $html = "";
                    foreach ($children as $child) {
                        $html .= '<li level='.$level.'>';
                        $html .= '    <span class="label-caption">'.$child->getLabel().'</span>';
                        $html .= '    <input type="text" name="label[]" style="display: none;" placeholder="Etichetta" value="'.$child->getLabel().'">&nbsp;&nbsp;';
                        $html .= '    <input type="text" name="url[]" style="display: none;" placeholder="Url" value="'.$child->getUrl().'">';
                        $html .= '    <input type="hidden" name="level[]" value="'.$level.'">';
                        $html .= '    <a class="edit-li"><i class="fa fa-pencil-square fa-lg"></i></a>';
                        $html .= '    <a class="delete-li"><i class="fa fa-minus-square fa-lg"></i></a>';
                        $html .= '    <a class="up-li"><i class="fa fa-arrow-up fa-lg"></i></a>';
                        $html .= '    <a class="down-li"><i class="fa fa-arrow-down fa-lg"></i></a>';
                        $html .= '    <ul>';
                        if ($child->getChildren() != null)
                            $html .= generateFor($child, ($level + 1));
                        $html .= '<li class="new-li" level='.($level + 1).'><a class="new-li-a"><i class="fa fa-plus-square fa-lg"></i></a></li>';
                        $html .= '    </ul>';
                        $html .= '</li>';
                    }
                    return $html;
                }
                ?>

                <li level=0 class="new-li"><a class="new-li-a"><i class="fa fa-plus-square fa-lg"></i></a></li>

                <li style="display: none;" id="li-template">
                    <span class="label-caption">Nuovo</span>
                    <input type="text" name="label[]" style="display: none;" placeholder="Etichetta">&nbsp;&nbsp;
                    <input type="text" name="url[]" style="display: none;" placeholder="Url">
                    <input type="hidden" name="order[]">
                    <input type="hidden" name="level[]">
                    <a  class="edit-li"><i class="fa fa-pencil-square fa-lg"></i></a>
                    <a class="delete-li" ><i class="fa fa-minus-square fa-lg"></i></a>
                    <a class="up-li" ><i class="fa fa-arrow-up fa-lg"></i></a>
                    <a class="down-li" ><i class="fa fa-arrow-down fa-lg"></i></a>
                    <ul>
                        <li class="new-li"><a class="new-li-a" ><i class="fa fa-plus-square fa-lg"></i></a></li>
                    </ul>
                </li>
            </ul>
        <!-- FINE AREA EDITING MENU -->

        </form>

    </div>

    <style>
        legend {
            margin-left: 10px;
        }
        .label-caption {
            display: inline-block;
            width: 150px;
            border-bottom: solid 1px #ccc;
        }
    </style>

    <script type="text/javascript" src="js/gestmenu.js"></script>

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