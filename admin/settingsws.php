<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 17.58
 */
require_once("../bootstrap.php");
$em = initializeEntityManager("../");

require_once("checkSessionForbidden.php");

if (!is_null($_POST['setkey'])) {
    $setting = $em->find('Model\Setting', $_POST['setkey']);
    $setting->setSettingValue($_POST['setvalue']);
    try {
        $em->merge($setting);
        $em->flush();
        exit("OK");
    } catch (PDOException $e) {
        echo "EXCEPTION => ".$e->getMessage();
        exit("\n\nTRACE => ".$e->getTraceAsString());
    }
}