<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 12:46
 */
namespace App\Admin\Module\FileManager;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class FileManagerControllerProvider implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/', '\App\Admin\Module\FileManager\FileManagerController::render')
            ->bind('admin.filemanager')
            ->before($app['moduleAuthorization.check']('FileManager', 'appAccess'));

        $controllers->match('/listContent', '\App\Admin\Module\FileManager\FileManagerController::listContent')
            ->bind('admin.filemanager.listContent')
            ->before($app['moduleAuthorization.check']('FileManager', 'listContent'));

        $controllers->match('/getDirectoryTree', '\App\Admin\Module\FileManager\FileManagerController::getDirectoryTree')
            ->bind('admin.filemanager.getDirectoryTree')
            ->before($app['moduleAuthorization.check']('FileManager', 'getDirectoryTree'));

        $controllers->match('/uploadFile', '\App\Admin\Module\FileManager\FileManagerController::uploadFile')
            ->bind('admin.filemanager.uploadFile')
            ->before($app['moduleAuthorization.check']('FileManager', 'uploadFile'));

        $controllers->match('/downloadFile', '\App\Admin\Module\FileManager\FileManagerController::downloadFile')
            ->bind('admin.filemanager.downloadFile')
            ->before($app['moduleAuthorization.check']('FileManager', 'downloadFile'));

        $controllers->match('/moveFile', '\App\Admin\Module\FileManager\FileManagerController::moveFile')
            ->bind('admin.filemanager.moveFile')
            ->before($app['moduleAuthorization.check']('FileManager', 'moveFile'));

        $controllers->match('/deleteFile', '\App\Admin\Module\FileManager\FileManagerController::deleteFile')
            ->bind('admin.filemanager.deleteFile')
            ->before($app['moduleAuthorization.check']('FileManager', 'deleteFile'));

        $controllers->match('/moveDirectory', '\App\Admin\Module\FileManager\FileManagerController::moveDirectory')
            ->bind('admin.filemanager.moveDirectory')
            ->before($app['moduleAuthorization.check']('FileManager', 'moveDirectory'));

        $controllers->match('/deleteDirectory', '\App\Admin\Module\FileManager\FileManagerController::deleteDirectory')
            ->bind('admin.filemanager.deleteDirectory')
            ->before($app['moduleAuthorization.check']('FileManager', 'deleteDirectory'));

        return $controllers;
    }

}