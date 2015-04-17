<?php
/**
 * User: Francesco
 * Date: 29/03/15
 * Time: 21:11
 */

namespace App\Admin\Module\Users;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class UsersModuleControllerProvider implements ControllerProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/', '\App\Admin\Module\Users\UsersController::listUsers')
            ->bind('admin.users.list');

        $controllers->match('/edit', '\App\Admin\Module\Users\EditorController::renderEditor')
            ->bind('admin.users.editNew');

        $controllers->match('/{id}/edit', '\App\Admin\Module\Users\EditorController::renderEditor')
            ->bind('admin.users.edit');

        $controllers->match('/{id}/delete', '\App\Admin\Module\Users\UsersController::delete')
            ->bind('admin.users.delete');

        $controllers->match('/checkUsername', '\App\Admin\Module\Users\UsersController::checkUsernameUnique')
            ->bind('admin.users.checkUsername');

        $controllers->match('/save', '\App\Admin\Module\Users\EditorController::save')
            ->bind('admin.users.insert');

        $controllers->match('/{id}/save', '\App\Admin\Module\Users\EditorController::save')
            ->bind('admin.users.update');

        return $controllers;
    }


} 