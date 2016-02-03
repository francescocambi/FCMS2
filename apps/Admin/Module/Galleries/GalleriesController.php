<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 19:42
 */

namespace App\Admin\Module\Galleries;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GalleriesController
{

    public function listGalleries(Application $app) {

        $galleries = $app['em']->getRepository('Plugin\Gallery\Model\Gallery')->findAll();
        $menublock = $app['admin.menu']->renderMenu('Galleries');

        return $app['twig']->render('App\\Admin\\Module\\Galleries\\List.twig', array(
            'menuBlock' => $menublock,
            'galleries' => $galleries
        ));

    }

    public function checkDataGalleryUnique(Application $app, Request $request) {
        $dataGallery = $request->get('datagallery');
        if (strlen($dataGallery) == 0)
            return new Response($app['admin.message_composer']->failureMessage("Datagallery argument not provided."), 400);

        $id = $request->get('id') or -1;
        try {
            $gallery = $app['em']->getRepository('Plugin\Gallery\Model\Gallery')->findOneBy(array("dataGallery" => $dataGallery));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->dataMessage(array(
            'unique' => (is_null($gallery) or $gallery->getId() == $id)
        ));
    }

    public function deleteGallery(Application $app, $id=null) {

        try {
            $gallery = $app['em']->find('Plugin\Gallery\Model\Gallery', $id);
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        if (is_null($gallery)) {
            return new Response($app['admin.message_composer']->failureMessage("Gallery not found."), 404);
        }

        try {
            $app['em']->beginTransaction();
            $app['em']->remove($gallery);
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