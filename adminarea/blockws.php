<?php

require_once("../bootstrap.php");

$em = initializeEntityManager("../");

require_once("checkSessionForbidden.php");

if ($_GET['action'] == "get") {

    $requestedBlock = $em->find('Model\ContentBlock', $_GET['blockid']);

    if (is_null($requestedBlock)) exit('{}');

    $mapping = array(
        "ID" => $requestedBlock->getId(),
        "NAME" => $requestedBlock->getName(),
        "DESCRIPTION" => $requestedBlock->getDescription(),
        "BLOCK_STYLE_ID" => $requestedBlock->getBlockStyleClassName(),
        "BG_URL" => $requestedBlock->getBgurl(),
        "BG_RED" => $requestedBlock->getBgred(),
        "BG_GREEN" => $requestedBlock->getBggreen(),
        "BG_BLUE" => $requestedBlock->getBgblue(),
        "BG_OPACITY" => $requestedBlock->getBgopacity(),
        "BG_REPEATX" => $requestedBlock->getBgrepeatx(),
        "BG_REPEATY" => $requestedBlock->getBgrepeaty(),
        "BG_SIZE" => $requestedBlock->getBgsize(),
        "CONTENT" => $requestedBlock->getContent()
    );

    //Encode object to JSON and print

    $result_string = "{";

    foreach ($mapping as $key => $value) {
        $result_string .= "\t".json_encode($key).": ".json_encode($value).",\n";
    }
    $result_string = substr($result_string, 0, strlen($result_string)-2);
    $result_string .= "\n}";

    echo $result_string;
}

if ($_GET['action'] == "checkname") {
    $name = $_GET['name'];
    if (isset($_GET['blockid']))
        $blockid = $_GET['blockid'];
    else
        $blockid = -1;
    $response = array();
    try {
        $block = $em->getRepository('Model\Block')->findOneBy(array("name" => $name));
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
        exit(-1);
    }
    $response['status'] = "ok";
    if (is_null($block) || $block->getId() == $blockid) {
        $response['result'] = "true";
    } else {
        $response['result'] = "false";
    }
    echo json_encode($response);
    exit(0);
}

if ($_GET['action'] == "delete") {

    $blockid = $_POST['blockid'];

    if (is_null($blockid)) exit();

    $block = $em->find('Model\Block', $blockid);

    if (is_null($block)) exit("NOT FOUND");

    try {
        $em->beginTransaction();
        $deletequery = $em->createQueryBuilder()
            ->delete()
            ->from('Model\PageBlock', "pb")
            ->where("pb.block=".$block->getId())
            ->getQuery();
        $deletequery->execute();
        $em->remove($block);
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
