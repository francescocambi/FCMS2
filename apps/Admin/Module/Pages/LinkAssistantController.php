<?php
/**
 * User: Francesco
 * Date: 23/04/15
 * Time: 12:02
 */

namespace App\Admin\Module\Pages;


use App\Admin\Module\Pages\LinkAssistant\Link;
use App\Admin\Module\Pages\LinkAssistant\LinkAssistant;
use Model\Url;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkAssistantController {

    public function linkRefactoringPreview(Application $app, Request $request) {
        $oldUrl = new Url();
        $oldUrl->setUrl($request->get('oldUrl'));

        $em = $app['em'];

        //Retrieve all blocks
        $blocks = $em->getRepository('\Model\ContentBlock')->findAll();

        $la = new LinkAssistant();

        $links = $la->searchLinks($blocks, $oldUrl);

        /**
         * @param Link $link
         * @return array
         */
        $callback = function ($link) {
            return array(
                'blockId' => $link->getBlock()->getId(),
                'blockName' => $link->getBlock()->getName(),
                'hrefAttribute' => $link->getDomElement()->getAttribute('href'),
                'linkBody' => $link->getDomElement()->xmltext()
            );
        };

        $responseObjects = array_map($callback, $links);

        return new Response(
            $app['admin.message_composer']->dataMessage($responseObjects)
        );

    }

    public function linkRefactoring(Application $app, Request $request) {

        $data = $request->get('refactoringBlocks');

        try {
            /** @var \Model\Url $oldUrl */
            $oldUrl = $app['em']->find('\Model\Url', $request->get('oldUrl'));
        } catch (Expcetion $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (!isset($oldUrl) || is_null($oldUrl)) {
            return new Response($app['admin.message_composer']->failureMessage(
                "Url to be refactored does not exist"
            ), 400);
        }

        $newUrl = new Url();
        $newUrl->setUrl($request->get('newUrl'));

        $blockIds = array();
        foreach ($data as $blockId => $val) {
            $blockIds[] = $blockId;
        }

        $blocks = $app['em']->getRepository('\Model\ContentBlock')->findBy(array('id' => $blockIds));

        $lass = new LinkAssistant();

        try {
            $updatedBlocks = $lass->refactorLinks($blocks, $oldUrl, $newUrl);

            //Saving blocks
            $app['em']->beginTransaction();
            foreach ($updatedBlocks as $block)
                $app['em']->merge($block);

            //Update url row with new url refactored

            $oldUrl->setUrl($newUrl->getUrl());
            $app['em']->merge($oldUrl);

            $app['em']->flush();
            $app['em']->commit();

        } catch (\Exception $e) {
            $app['em']->rollback();
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return new Response($app['admin.message_composer']->successMessage(), 200);

    }

}