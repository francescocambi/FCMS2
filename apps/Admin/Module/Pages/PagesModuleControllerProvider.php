<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 16:57
 */

namespace App\Admin\Module\Pages;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class PagesModuleControllerProvider implements ControllerProviderInterface {

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];


        $controllers->match('/', '\App\Admin\Module\Pages\PagesController::listPages')
            ->bind("admin.pages.listPages");

        $controllers->match('/edit', '\App\Admin\Module\Pages\EditorController::renderEditor')
            ->bind("admin.pages.editNewPage");

        $controllers->match('/{id}/edit', '\App\Admin\Module\Pages\EditorController::renderEditor')
            ->bind("admin.pages.editPage");

        $controllers->match('/{id}/delete', '\App\Admin\Module\Pages\PagesController::deletePage')
            ->bind("admin.pages.deletePage");

        $controllers->match('/{id}/duplicate', '\App\Admin\Module\Pages\PagesController::duplicatePage')
            ->bind("admin.pages.duplicatePage");

        $controllers->match('/checkPageName', '\App\Admin\Module\Pages\PagesController::checkNameUnique')
            ->bind("admin.pages.checkNameUnique");

        $controllers->match('/save', '\App\Admin\Module\Pages\EditorController::insertPage')
            ->bind("admin.pages.insertPage");

        $controllers->match('/{id}/save', '\App\Admin\Module\Pages\EditorController::updatePage')
            ->bind("admin.pages.updatePage");


        return $controllers;
    }

} 