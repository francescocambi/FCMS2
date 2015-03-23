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
            ->bind("admin.languages.list");

        $controllers->match('/edit', '\App\Admin\Module\Languages\EditorController::renderEditor')
            ->bind("admin.languages.editNew");

        $controllers->match('/{id}/edit', '\App\Admin\Module\Languages\EditorController::renderEditor')
            ->bind("admin.languages.edit");

        $controllers->match('/{id}/delete', '\App\Admin\Module\Languages\LanguagesController::deleteLanguage')
            ->bind("admin.languages.delete");

        $controllers->match('/checkCode', '\App\Admin\Module\Languages\LanguagesController::checkCodeUnique')
            ->bind("admin.languages.checkCode");

        $controllers->match('/save', '\App\Admin\Module\Languages\EditorController::saveLanguage')
            ->bind("admin.languages.insert");

        $controllers->match('/{id}/save', '\App\Admin\Module\Languages\EditorController::saveLanguage')
            ->bind("admin.languages.update");

//        $controllers->match('/{id}', '\App\Admin\Module\Blocks\BlocksController::getBlock')
//            ->bind("admin.language.getBlock");

        return $controllers;

    }

} 