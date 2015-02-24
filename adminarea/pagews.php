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

if ($_GET['action'] == "duplicate") {

    $pageid = $_POST['pageid'];

    if (is_null($pageid)) exit();

    $duplicate_blocks = $_POST['duplicateblocks'] == 'true';
    $response = array();

    $page = $em->find('Model\Page', $pageid);

    if (is_null($page)) {
        $response['result'] = false;
        $response['exception'] = "PAGE NOT FOUND";
        echo json_encode($response);
        exit(0);
    }

    try {
        $em->beginTransaction();
        $date = getdate();
        $timestamp = $date['mday']."-".$date['mon']."-".$date['year']."_".$date['hours'].":".$date['minutes'].":".$date['seconds'];

        //Retrieves pageblock list in page
        $blocks_in_page = $page->getPageBlocksArray();

        if ($duplicate_blocks) {
            //Clone Blocks
            $new_blocks_array = array();
            foreach ($blocks_in_page as $pageblock) {
                //clone it
                $newblock = clone $pageblock->getBlock();
                //change name to another unique name with timestamp
                $newblock->setName($newblock->getName() . "_" . $timestamp);
                //and persists newly created block
                $em->persist($newblock);
                array_push($new_blocks_array, $newblock);
            }
            $em->flush();
        } else {
            $new_blocks_array = array();
            foreach ($blocks_in_page as $pageblock) {
                array_push($new_blocks_array, $pageblock->getBlock());
            }
        }

        //Clone page
        $newpage = clone $page;
        //Create a new unique name with timestamp
        $name = $newpage->getName()."_".$timestamp;
        $newpage->setName($name);
        $newpage->setPageUrls(new \Doctrine\Common\Collections\ArrayCollection());
        $em->persist($newpage);
        $em->flush();

        //Creates pageblock objects that bind cloned blocks to cloned page
        $block_order = 0;
        foreach ($new_blocks_array as $newblock) {
            $new_pageblock = new Model\PageBlock();
            $new_pageblock->setPage($newpage);
            $new_pageblock->setBlock($newblock);
            $new_pageblock->setBlockOrder($block_order++);
            $em->persist($new_pageblock);
        }
        $em->flush();

        //Deep clones page identified by pageid
        //If all goes right print OK clone_page_id
        //All blocks must be cloned
        $em->commit();

        $response['result'] = true;
        $response['id'] = $newpage->getId();
        $response['name'] = $newpage->getName();
    } catch (Exception $e) {
        $em->rollback();
        $response['result'] = false;
        $response['exception'] = "EXCEPTION: ".$e->getMessage();
        $response['trace'] = "TRACE => ".$e->getTraceAsString();
    }

    echo json_encode($response);
    exit(0);

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