<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 17:01
 */

namespace App\Admin\Module\Pages;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        try {
            /** @var \Model\Page $page */
            $page = $em->find('Model\Page', $pageid);
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (is_null($page)) {
            return new Response($app['admin.message_composer']->failureMessage('Page not found.'), 404);
        }

        try {
            $em->beginTransaction();
            foreach ($page->getPageBlocks() as $pageblock)
                $em->remove($pageblock);
            $em->remove($page);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->successMessage();
    }

    public function duplicatePage(Application $app, Request $request, $id) {
        $em = $app['em'];

        $duplicate_blocks = ($request->get('duplicateblocks') == 'true');
        $response = array();

        try {
            /** @var \Model\Page $page */
            $page = $em->find('Model\Page', $id);
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (is_null($page)) {
            return new Response($app['admin.message_composer']->failureMessage("Page not found."), 404);
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

            return $app['admin.message_composer']->dataMessage(array(
                'id' => $newpage->getId(),
                'name' => $newpage->getName()
            ));
        } catch (\Exception $e) {
            $em->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }
    }

    public function checkNameUnique(Application $app, Request $request) {
        $name = $request->get('name');

        if (strlen($name) == 0) {
            return new Response($app['admin.message_composer']->failureMessage("Name argument not provided."), 400);
        }

        $id = $request->get('pageid') or -1;
        try {
            $page = $app['em']->getRepository('Model\Page')->findOneBy(array("name" => $name));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->dataMessage(array(
            'unique' => (is_null($page) || $page->getId() == $id)
        ));
    }

    

} 