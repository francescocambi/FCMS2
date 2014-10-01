<?php
require_once("../bootstrap.php");

$em = initializeEntityManager("../");

//TODO: Verify html session

$pageid = $_POST['pageid'];

if (is_null($pageid)) exit();

$page = $em->find('Model\Page', $pageid);

if (is_null($page)) exit("PAGE NOT FOUND");

try {
    foreach ($page->getPageBlocks() as $pageblock)
        $em->remove($pageblock);
    foreach ($page->getAccessGroups() as $accessgroup)
        $em->remove($accessgroup);
    $em->remove($page);
    $em->flush();
} catch (Exception $e) {
    echo "EXCEPTION: ".$e->getMessage();
    echo "\n\nTRACE => ".$e->getTraceAsString();
    exit();
}

echo "OK";