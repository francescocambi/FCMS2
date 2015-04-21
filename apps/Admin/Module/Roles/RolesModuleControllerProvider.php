<?php
/**
 * User: Francesco
 * Date: 17/04/15
 * Time: 15:45
 */

namespace App\Admin\Module\Roles;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class RolesModuleControllerProvider implements ControllerProviderInterface {

    /**
     * @{@inheritdoc}
     */
    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/', '\App\Admin\Module\Roles\RolesController::listRoles')
            ->bind('admin.roles.list');

        $controllers->match('/edit', '\App\Admin\Module\Roles\EditorController::renderEditor')
            ->bind('admin.roles.editNew');

        $controllers->match('/{id}/edit', '\App\Admin\Module\Roles\EditorController::renderEditor')
            ->bind('admin.roles.edit');

        $controllers->match('/{id}/delete', '\App\Admin\Module\Roles\RolesController::delete')
            ->bind('admin.roles.delete');

        $controllers->match('/checkName', '\App\Admin\Module\Roles\RolesController::checkNameUnique')
            ->bind('admin.roles.checkName');

        $controllers->match('/save', '\App\Admin\Module\Roles\EditorController::save')
            ->bind('admin.roles.insert');

        $controllers->match('/{id}/save', '\App\Admin\Module\Roles\EditorController::save')
            ->bind('admin.roles.update');

        return $controllers;
    }


} 