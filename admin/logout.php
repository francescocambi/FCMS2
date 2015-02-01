<?php
/**
 * User: Francesco
 * Date: 30/10/14
 * Time: 18:17
 */
require_once("../bootstrap.php");

$em = initializeEntityManager("../");
require_once("checkSessionRedirect.php");

$handler = new Authentication\HttpSessionHandler();
$handler->closeSession();
$session->setClosingTimestamp(new DateTime());
try {
    $em->merge($session);
} catch (Exception $e) {

}
header("Location: /admin/index.php");