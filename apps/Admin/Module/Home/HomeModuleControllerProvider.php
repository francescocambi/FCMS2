<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 12:46
 */
namespace App\Admin\Module\Home;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class HomeModuleControllerProvider implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];


        $controllers->get('/', '\App\Admin\Module\Home\HomeController::render')
        ->bind("admin.home");


        return $controllers;
    }

}