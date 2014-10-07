<?php
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionForbidden.php");

if ($_GET['action'] == "delete") {

    $pageid = $_POST['pageid'];

    if (is_null($pageid)) exit();

    $page = $em->find('Model\Page', $pageid);

    if (is_null($page)) exit("PAGE NOT FOUND");

    try {
        $em->beginTransaction();
        foreach ($page->getPageBlocks() as $pageblock)
            $em->remove($pageblock);
        foreach ($page->getAccessGroups() as $accessgroup)
            $em->remove($accessgroup);
        $em->remove($page);
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

if ($_GET['action'] == "checkname") {
    $name = $_GET['name'];
    if (isset($_GET['pageid']))
        $id = $_GET['pageid'];
    else
        $id = -1;
    try {
        $page = $em->getRepository('Model\Page')->findOneBy(array("name" => $name));
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
        exit(-1);
    }
    $response['status'] = "ok";
    if (is_null($page) || $page->getId() == $id) {
        $response['result'] = "true";
    } else {
        $response['result'] = "false";
    }
    echo json_encode($response);
    exit(0);



}