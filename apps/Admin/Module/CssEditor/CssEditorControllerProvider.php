<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 12:46
 */
namespace App\Admin\Module\CssEditor;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class CssEditorControllerProvider implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];


        $controllers->match('/', '\App\Admin\Module\CssEditor\CssEditorController::render')
            ->bind("admin.csseditor");

        $controllers->match('/fileContent', '\App\Admin\Module\CssEditor\CssEditorController::getFileContent')
            ->bind("admin.csseditor.getFileContent");

        $controllers->post('/saveFile', '\App\Admin\Module\CssEditor\CssEditorController::saveFile')
            ->bind("admin.csseditor.saveFile");


        return $controllers;
    }

}