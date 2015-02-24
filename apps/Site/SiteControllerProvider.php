<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 14:27
 */

namespace App\Site;


use Silex\Application;
use Silex\ControllerProviderInterface;

class SiteControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {

        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/{lang}/', 'page.controller:renderPage')
            ->assert('lang', '[a-z]{2}')
            ->bind('languageOnly');

        $controllers->match('/{url}', 'page.controller:renderPage')
            ->assert('url', '[a-z\-]{3}[a-z\-]+')
            ->value('url', 'home_it')
            ->bind('urlOnly');

        $controllers->match('/{lang}/{url}', 'page.controller:renderPage')
            ->assert('lang','[a-z]{2}')
            ->bind('languageAndUrl');

        return $controllers;

    }

} 