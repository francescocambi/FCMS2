<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 17:01
 */

namespace App\Admin\Module\Pages;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PagesController {

    public function listPages(Application $app) {

        $pages = $app['em']->getRepository('Model\Page')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu("Pages");

        return $app['twig']->render('App\\Admin\\Module\\Pages\\List.twig', array(
            'menuBlock' => $menuBlock,
            'pages' => $pages
        ));

    }

    public function deletePage(Application $app, $id=null) {
        $em = $app['em'];
        $pageid = $id;

        if (is_null($pageid)) return null;

        $page = $em->find('Model\Page', $pageid);

        if (is_null($page)) return "PAGE NOT FOUND";

        try {
            $em->beginTransaction();
            foreach ($page->getPageBlocks() as $pageblock)
                $em->remove($pageblock);
            foreach ($page->getAccessGroups() as $accessgroup)
                $em->remove($accessgroup);
            $em->remove($page);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            $errorMessage = "EXCEPTION: ".$e->getMessage();
            $errorMessage .= "\n\nTRACE => ".$e->getTraceAsString();
            return $errorMessage;
        }

        return "OK";
    }

    public function duplicatePage(Application $app, Request $request, $id=null) {
        $pageid = $id;

        $em = $app['em'];

        if (is_null($pageid)) return null;

        $duplicate_blocks = ($request->get('duplicateblocks') == 'true');
        $response = array();

        $page = $em->find('Model\Page', $pageid);

        if (is_null($page)) {
            $response['result'] = false;
            $response['exception'] = "PAGE NOT FOUND";
            return json_encode($response);
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
                $new_pageblock = new \Model\PageBlock();
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
        } catch (\Exception $e) {
            $em->rollback();
            $response['result'] = false;
            $response['exception'] = "EXCEPTION: ".$e->getMessage();
            $response['trace'] = "TRACE => ".$e->getTraceAsString();
        }

        return json_encode($response);
    }

    public function checkNameUnique(Application $app, Request $request) {
        $name = $request->get('name');

        if (strlen($name) == 0) {
            $response['status'] = false;
            $response['errormessage'] = "Name argument not provided.";
            return json_encode($response);
        }

        $id = $request->get('pageid') or -1;
        try {
            $page = $app['em']->getRepository('Model\Page')->findOneBy(array("name" => $name));
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['errormessage'] = "EXCEPTION => ".$e->getMessage()."\n\nTRACE => ".$e->getTraceAsString();
            return $response;
        }
        $response['status'] = "ok";
        if (is_null($page) || $page->getId() == $id) {
            $response['result'] = true;
        } else {
            $response['result'] = false;
        }
        return json_encode($response);
    }

    

} 