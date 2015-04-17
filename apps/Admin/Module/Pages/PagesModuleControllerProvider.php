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
use Symfony\Component\HttpFoundation\Response;

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


        /**
         * Altre cose da rivedere
         * >> Il menu dell'amministrazione viene ordinato secondo le autorizzazioni modulo-ruolo
         *      prevedere invece un ordinamento codificato eventualmente in un campo sulla tabella moduli
         * >> Valutare performance di questa soluzione
         * >> Sviluppare gestione ruoli con abbinamento diritti ruolo-modulo
         */
        $controllers->match('/', '\App\Admin\Module\Pages\PagesController::listPages')
            ->bind("admin.pages.listPages")
            ->before($app['moduleAuthorization.check']('Pages', 'listPages'));

        $controllers->match('/edit', '\App\Admin\Module\Pages\EditorController::renderEditor')
            ->bind("admin.pages.editNewPage")
            ->before($app['moduleAuthorization.check']('Pages', 'editNewPage'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Pages\EditorController::renderEditor')
            ->bind("admin.pages.editPage")
            ->before($app['moduleAuthorization.check']('Pages', 'editPage'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Pages\PagesController::deletePage')
            ->bind("admin.pages.deletePage")
            ->before($app['moduleAuthorization.check']('Pages', 'deletePage'));

        $controllers->match('/{id}/duplicate', '\App\Admin\Module\Pages\PagesController::duplicatePage')
            ->bind("admin.pages.duplicatePage")
            ->before($app['moduleAuthorization.check']('Pages', 'duplicatePage'));

        $controllers->match('/checkPageName', '\App\Admin\Module\Pages\PagesController::checkNameUnique')
            ->bind("admin.pages.checkNameUnique")
            ->before($app['moduleAuthorization.check']('Pages', 'checkNameUnique'));

        $controllers->match('/save', '\App\Admin\Module\Pages\EditorController::insertPage')
            ->bind("admin.pages.insertPage")
            ->before($app['moduleAuthorization.check']('Pages', 'insertPage'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Pages\EditorController::updatePage')
            ->bind("admin.pages.updatePage")
            ->before($app['moduleAuthorization.check']('Pages', 'updatePage'));


        return $controllers;
    }

} 