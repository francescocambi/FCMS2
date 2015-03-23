<?php
/**
 * User: Francesco
 * Date: 05/03/15
 * Time: 17:47
 */

namespace App\Admin\Module\Menu;


use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class MenuController {

    public function listMenu(Application $app) {

        $menuBlock = $app['admin.menu']->renderMenu('Menu');

        $menus = $app['em']->getRepository('Model\Menu')->findAll();

        return $app['twig']->render('App\\Admin\\Module\\Menu\\List.twig', array(
           'menus' => $menus,
            'menuBlock' => $menuBlock
        ));

    }

    public function deleteMenu(Application $app, $id) {

        $menu = $app['em']->find('Model\Menu', $id);

        if (is_null($menu)) {
            return new Response($app['admin.message_composer']->failureMessage('Menu not found.'), 404);
        }

        try {
            $app['em']->beginTransaction();

            $app['em']->remove($menu);
            $app['em']->flush();
            $app['em']->commit();
        } catch (\Exception $e) {
            $app['em']->rollback();
            $app['monolog']->addError($e->getMessage());
            new Response($app['admin.message_composer']->exceptionMessage($e), 500);
        }

        return $app['admin.message_composer']->successMessage();
    }

} 