<?php
/**
 * User: Francesco
 * Date: 28/02/15
 * Time: 19:04
 */

namespace App\Admin\Module\Blocks;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BlocksController {

    public function listBlocks(Application $app) {

        $blocks = $app['em']->getRepository('Model\Block')->findAll();

        $menublock = $app['admin.menu']->renderMenu('Blocks');

        return $app['twig']->render('App\\Admin\\Module\\Blocks\\List.twig', array(
            'menuBlock' => $menublock,
            'blocks' => $blocks,
        ));

    }

    public function checkNameUnique(Application $app, Request $request) {
        $name = $request->get('name');

        if (strlen($name) == 0) {
            return $app['admin.message_composer']->failureMessage("Name argument not provided.");
        }

        $blockid = $request->get('blockid') or -1;
        try {
            $block = $app['em']->getRepository('Model\Block')->findOneBy(array("name" => $name));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }
        if (is_null($block) || $block->getId() == $blockid) {
            $data = array('unique' => true);
        } else {
            $data = array('unique' => false);
        }
        return $app['admin.message_composer']->dataMessage($data);
    }

    /**
     * @param Application $app
     * @param int $id
     * @return string
     */
    public function getBlock(Application $app, $id) {

        try {
            //Try to find block as contentblock
            $requestedBlock = $app['em']->find('Model\ContentBlock', $id);
            //If can't find it search it as a general block
            if (is_null($requestedBlock)) {
                $requestedBlock = $app['em']->find('Model\Block', $id);
                //That isn't editable
                $editable = false;
                $content = $requestedBlock->getHTML($app);
            } else {
                //Content blocks are editable
                $editable = true;
                $content = $requestedBlock->getContent();
            }

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (is_null($requestedBlock)) {
            return new Response($app['admin.message_composer']->failureMessage("Block not found."), 404);
        }

         $data = array(
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
            "CONTENT" => $content,
             "EDITABLE" => $editable
        );

        return $app['admin.message_composer']->dataMessage($data);
    }

    public function deleteBlock(Application $app, $id=null) {

        try {
            $block = $app['em']->find('Model\Block', $id);
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (is_null($block)) {
            return new Response($app['admin.message_composer']->failureMessage("Block not found."), 404);
        }

        try {
            $app['em']->beginTransaction();
            $deletequery = $app['em']->createQueryBuilder()
                ->delete()
                ->from('Model\PageBlock', "pb")
                ->where("pb.block=".$block->getId())
                ->getQuery();
            $deletequery->execute();
            $app['em']->remove($block);
            $app['em']->flush();
            $app['em']->commit();
        } catch (\Exception $e) {
            $app['em']->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);

        }

        return $app['admin.message_composer']->successMessage();
    }

} 