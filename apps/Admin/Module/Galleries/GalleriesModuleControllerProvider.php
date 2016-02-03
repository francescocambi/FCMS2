<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 17:43
 */

namespace App\Admin\Module\Galleries;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class GalleriesModuleControllerProvider implements ControllerProviderInterface
{

    public function connect(Application $app) {

        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/', '\App\Admin\Module\Galleries\GalleriesController::listGalleries')
            ->bind("admin.galleries.list")
            ->before($app['moduleAuthorization.check']('Galleries', 'listGalleries'));

        $controllers->match('/edit', '\App\Admin\Module\Galleries\EditorController::renderEditor')
            ->bind("admin.galleries.editNew")
            ->before($app['moduleAuthorization.check']('Galleries', 'editNew'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Galleries\EditorController::renderEditor')
            ->bind("admin.galleries.edit")
            ->before($app['moduleAuthorization.check']('Galleries', 'edit'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Galleries\GalleriesController::deleteGallery')
            ->bind("admin.galleries.delete")
            ->before($app['moduleAuthorization.check']('Galleries', 'delete'));

        $controllers->match('/save', '\App\Admin\Module\Galleries\EditorController::insertGallery')
            ->bind('admin.galleries.insert')
            ->before($app['moduleAuthorization.check']('Galleries', 'insert'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Galleries\EditorController::updateGallery')
            ->bind('admin.galleries.update')
            ->before($app['moduleAuthorization.check']('Galleries', 'update'));

        $controllers->match('/checkDataGallery', '\App\Admin\Module\Galleries\GalleriesController::checkDataGalleryUnique')
            ->bind('admin.galleries.checkDataGalleryUnique')
            ->before($app['moduleAuthorization.check']('Galleries', 'checkDataGalleryUnique'));


        return $controllers;
    }

}