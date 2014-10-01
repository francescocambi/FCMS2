<?php

require_once("../bootstrap.php");

$em = initializeEntityManager("../");

//TODO: Verify html session

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

