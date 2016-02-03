<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 19:45
 */

namespace App\Admin\Module\Galleries;


use Doctrine\ORM\EntityManager;
use Plugin\Gallery\Model\Gallery;
use Plugin\Gallery\Model\GalleryItem;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorController
{

    public function renderEditor(Application $app, Request $request, $id=null) {

        $exception = null;
        $success = ($request->get('s') == 'true');

        if (is_null($id))
            $gallery = new Gallery();
        else {
            try {
                $gallery = $app['em']->find('Plugin\Gallery\Model\Gallery', $id);
            } catch (\Exception $e) {
                $app['monolog']->addError($e->getMessage());
                $exception = array(
                    'mesage' => 'EXCEPTION >> '.$e->getMessage(),
                    'traceString' => $e->getTraceAsString()
                );
                $gallery = new Gallery();
            }
        }

        return $app['twig']->render('App\Admin\Module\Galleries\Editor.twig', array (
            'gallery' => $gallery,
            'success' => $success,
            'menuBlock' => $app['admin.menu']->renderMenu("Galleries"),
            'exception' => $exception
        ));

    }

    public function insertGallery(Application $app, Request $request) {
        $data = $request->request->all();

        try {
            $newid = $this->insertUpdateProcessing($app['em'], $data);

            return $app->redirect(
                $app['url_generator']->generate('admin.galleries.edit', array(
                    'id' => $newid
                ))."?s=true"
            );
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            )), 500);
        }
    }

    public function updateGallery(Application $app, Request $request, $id) {
        $data = $request->request->all();

        var_dump($data);

        try {
            $this->insertUpdateProcessing($app['em'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.galleries.edit', array(
                        'id' => $id
                    ))."?s=true"
            );

        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(var_export(array(
                'status' => false,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            )), 500);
        }
    }

    private function insertUpdateProcessing(EntityManager $em, $data, $id=null) {
        $update = !is_null($id);

        try {
            $em->beginTransaction();

            if ($update) {
                $gallery = $em->getRepository('Plugin\Gallery\Model\Gallery')->find($id);
            } else {
                $gallery = new Gallery();
            }

            if ($update)
                foreach ($gallery->getPhotos()->toArray() as $item)
                    $em->remove($item);

            $gallery->setName($data['name']);
            $gallery->setDescription($data['description']);
            $gallery->setDataGallery($data['dataGallery']);
            $gallery->setThumbImage($data['thumbImage']);

            if ($update)
                $em->merge($gallery);
            else
                $em->persist($gallery);
            $em->flush();

            for ($i = 0; $i < count($data['photos']['imageUrl']); $i++) {
                $photo = new GalleryItem();
                $photo->setImageUrl($data['photos']['imageUrl'][$i]);
                $photo->setPhotoOrder($data['photos']['order'][$i]);
                $gallery->addPhoto($photo);
            }
            $em->merge($gallery);
            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return $gallery->getId();

    }

}