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
            ->bind('admin.users.list')
            ->before($app['moduleAuthorization.check']('Users', 'listUsers'));

        $controllers->match('/edit', '\App\Admin\Module\Users\EditorController::renderEditor')
            ->bind('admin.users.editNew')
            ->before($app['moduleAuthorization.check']('Users', 'editNew'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Users\EditorController::renderEditor')
            ->bind('admin.users.edit')
            ->before($app['moduleAuthorization.check']('Users', 'edit'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Users\UsersController::delete')
            ->bind('admin.users.delete')
            ->before($app['moduleAuthorization.check']('Users', 'delete'));

        $controllers->match('/checkUsername', '\App\Admin\Module\Users\UsersController::checkUsernameUnique')
            ->bind('admin.users.checkUsername')
            ->before($app['moduleAuthorization.check']('Users', 'checkUsername'));

        $controllers->match('/save', '\App\Admin\Module\Users\EditorController::save')
            ->bind('admin.users.insert')
            ->before($app['moduleAuthorization.check']('Users', 'insert'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Users\EditorController::save')
            ->bind('admin.users.update')
            ->before($app['moduleAuthorization.check']('Users', 'update'));

        return $controllers;
    }


} 