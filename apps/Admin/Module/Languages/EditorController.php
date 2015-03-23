<?php
/**
 * User: Francesco
 * Date: 06/03/15
 * Time: 12:37
 */

namespace App\Admin\Module\Languages;


use Doctrine\ORM\EntityManager;
use Model\Language;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EditorController {

    public function renderEditor(Application $app, Request $request, $id=null) {

        $success = ($request->get('s') == "true");
        $exception = null;

        if (is_null($id))
            $language = new Language();
        else {
            try {
                $language = $app['em']->find('Model\Language', $id);
            } catch (\Exception $e) {
                $exception = $e;
                $language = new Language();
                $app['monolog']->addError($e->getMessage());
            }
        }

        $menus = $app['em']->getRepository('Model\Menu')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu('Languages');

        return $app['twig']->render('App\\Admin\\Module\\Languages\\Editor.twig', array(
            'success' => $success,
            'exception' => $exception,
            'language' => $language,
            'menus' => $menus,
            'menuBlock' => $menuBlock
        ));

    }

    public function insertUpdateProcessing(EntityManager $em, $data, $id=null) {
        $update = !is_null($id);

        try {
            $em->beginTransaction();

            if ($update) {
                $language = $em->find('Model\Language', $id);
            } else {
                $language = new Language();
            }

            $language->setCode($data['code']);
            $language->setDescription($data['description']);
            $language->setFlagImageURL($data['flagimageurl']);
            $menu = $em->find('Model\Menu', $data['menuid']);
            $language->setMenu($menu);

            if ($update)
                $em->merge($language);
            else
                $em->persist($language);

            $em->flush();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }

        return $language->getId();

    }

    public function saveLanguage(Application $app, Request $request, $id=null) {

        $data = $request->request->all();

        try {
            $processedId = $this->insertUpdateProcessing($app['em'], $data, $id);

            return $app->redirect(
                $app['url_generator']->generate('admin.languages.edit', array('id' => $processedId))."?s=true"
            );
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response(
                $app['admin.message_composer']->exceptionMessage($e), 500
            );
        }

    }



} 