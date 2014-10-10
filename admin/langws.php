<?php
/**
 * User: francesco
 * Date: 10/10/14
 * Time: 12:18 PM
 */

require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionForbidden.php");

if ($_GET['action'] == "checkcode") {
    $code = $_GET['code'];
    if (isset($_GET['langid']))
        $langid = $_GET['langid'];
    else
        $langid = -1;
    $response = array();
    try {
        $language = $em->getRepository('Model\Language')->findOneBy(array("code" => $code));
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
        exit(-1);
    }
    $response['status'] = "ok";
    if (is_null($language) || $language->getId() == $langid) {
        $response['result'] = "true";
    } else {
        $response['result'] = "false";
    }
    echo json_encode($response);
    exit(0);
}

if ($_GET['action'] == "delete") {

    $langid = $_POST['langid'];

    if (is_null($langid)) exit();

    $language = $em->find('Model\Language', $langid);

    if (is_null($language)) exit("NOT FOUND");

    try {
        $em->beginTransaction();
        $em->remove($language);
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
