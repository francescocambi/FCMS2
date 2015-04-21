<?php
/**
 * User: Francesco
 * Date: 28/02/15
 * Time: 19:04
 */

namespace App\Admin\Module\Languages;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class LanguagesModuleControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {

        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/', '\App\Admin\Module\Languages\LanguagesController::listLanguages')
            ->bind("admin.languages.list")
            ->before($app['moduleAuthorization.check']('Languages', 'listLanguages'));

        $controllers->match('/edit', '\App\Admin\Module\Languages\EditorController::renderEditor')
            ->bind("admin.languages.editNew")
            ->before($app['moduleAuthorization.check']('Languages', 'editNewLanguage'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Languages\EditorController::renderEditor')
            ->bind("admin.languages.edit")
            ->before($app['moduleAuthorization.check']('Languages', 'editLanguage'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Languages\LanguagesController::deleteLanguage')
            ->bind("admin.languages.delete")
            ->before($app['moduleAuthorization.check']('Languages', 'deleteLanguage'));

        $controllers->match('/checkCode', '\App\Admin\Module\Languages\LanguagesController::checkCodeUnique')
            ->bind("admin.languages.checkCode")
            ->before($app['moduleAuthorization.check']('Languages', 'checkCode'));

        $controllers->match('/save', '\App\Admin\Module\Languages\EditorController::saveLanguage')
            ->bind("admin.languages.insert")
            ->before($app['moduleAuthorization.check']('Languages', 'insertLanguage'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Languages\EditorController::saveLanguage')
            ->bind("admin.languages.update")
            ->before($app['moduleAuthorization.check']('Languages', 'updateLanguage'));

        return $controllers;

    }

} 