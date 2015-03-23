<?php
/**
 * User: Francesco
 * Date: 06/03/15
 * Time: 11:21
 */

namespace App\Admin\Module\Languages;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguagesController {

    public function listLanguages(Application $app) {
        $languages = $app['em']->getRepository('Model\Language')->findAll();

        $menuBlock = $app['admin.menu']->renderMenu('Languages');

        return $app['twig']->render('App\\Admin\\Module\\Languages\\List.twig', array(
            'languages' => $languages,
            'menuBlock' => $menuBlock
        ));
    }

    public function checkCodeUnique(Application $app, Request $request) {
        $code = $request->get('code');
        $id = $request->get('id') or -1;

        try {
            $language = $app['em']->getRepository('Model\Language')->findOneBy(array("code" => $code));
        } catch (\Exception $e) {
            $app['monolog']->addError($e->getMessage());
            return new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return new Response($app['admin.message_composer']->dataMessage(array(
            'unique' => (is_null($language) || $language->getId() == $id)
        )));
    }

    public function deleteLanguage(Application $app, $id) {

        $language = $app['em']->find('Model\Language', $id);

        if (is_null($language)) {
            return new Response($app['admin.message_composer']->failureMessage("Language not found."), 404);
        }

        try {
            $app['em']->beginTransaction();
            $app['em']->remove($language);
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