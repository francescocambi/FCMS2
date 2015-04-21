<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 16:57
 */

namespace App\Admin\Module\Menu;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class MenuModuleControllerProvider implements ControllerProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];


        $controllers->match('/', '\App\Admin\Module\Menu\MenuController::listMenu')
            ->bind("admin.menu.listMenu")
            ->before($app['moduleAuthorization.check']('Menu', 'listMenu'));

        $controllers->match('/edit', '\App\Admin\Module\Menu\EditorController::renderEditor')
            ->bind("admin.menu.editNew")
            ->before($app['moduleAuthorization.check']('Menu', 'editNewMenu'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Menu\EditorController::renderEditor')
            ->bind("admin.menu.edit")
            ->before($app['moduleAuthorization.check']('Menu', 'editMenu'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Menu\MenuController::deleteMenu')
            ->bind("admin.menu.deleteMenu")
            ->before($app['moduleAuthorization.check']('Menu', 'deleteMenu'));

        $controllers->match('/save', '\App\Admin\Module\Menu\EditorController::insertMenu')
            ->bind("admin.menu.insertMenu")
            ->before($app['moduleAuthorization.check']('Menu', 'insertMenu'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Menu\EditorController::updateMenu')
            ->bind("admin.menu.updateMenu")
            ->before($app['moduleAuthorization.check']('Menu', 'updateMenu'));


        return $controllers;
    }

} 