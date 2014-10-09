<?php
error_reporting(E_ALL);
/**
 * User: francesco
 * Date: 10/9/14
 * Time: 10:45 AM
 */
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionForbidden.php");


if ($_GET['action'] == "delete") {

    $menuid = $_POST['menuid'];

    if (is_null($menuid)) exit();

    $menu = $em->find('Model\Menu', $menuid);

    if (is_null($menu)) exit("NOT FOUND");

    try {
        $em->beginTransaction();

        $em->remove($menu);
        $em->flush();
        $em->commit();
    } catch (Exception $e) {
        $em->rollback();
        echo "EXCEPTION: ".$e->getMessage();
        echo "\n\nTRACE => ".$e->getTraceAsString();
        exit();
    }

    echo "OK";

}