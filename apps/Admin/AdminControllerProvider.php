<?php

/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 12:30
 */

namespace App\Admin;

use App\Admin\Menu\Menu;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class AdminControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {

        //TODO - Loads Admin app context
        $app['admin.menu'] = $app->share(function () use ($app) {
            return new Menu($app);
        });

        $app['admin.message_composer'] = $app->share(function () use ($app) {
            return new JSONMessageComposer();
        });

        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function () use ($app) {
            return $app->redirect('home');
        });

        $controllerProviders = $this->retrieveModulesControllerProviders();
        /*
         * Iterates over controllerProviders and bind each moduleName in the url
         * with the controller provider responsible for that module
         */

        foreach ($controllerProviders as $controllerProvider) {
            if (class_exists($controllerProvider['controllerProviderName'])) {
                //FIXME Check if controllerProvider implements ControllerProviderInterface
                $controllerProviderObject = new $controllerProvider['controllerProviderName']();
                $controllers->mount('/' . strtolower($controllerProvider['moduleName']),
                    $controllerProviderObject->connect($app));
            } else {
                //TODO Log error cannot find class controllerProviderName
            }
        }

        return $controllers;
    }

    /**
     * Inspects module folders to find ControllerProvider class for each module
     * Returns an array containing for each module an associative array with this fields:
     * [moduleName, controllerProviderName]
     * @return array
     */
    public function retrieveModulesControllerProviders() {
        $retrievedControllerProviders = array();
        //directory is the Module directory
        $directory = __DIR__.DIRECTORY_SEPARATOR.'Module';
        //gets its content and iterates over it
        $modules = scandir($directory);
        foreach ($modules as $module) {
            //Ignore . and .. dirs
            if ($module != "." && $module != ".." && is_dir($directory.DIRECTORY_SEPARATOR.$module)) {
                //Checks if there is a ControllerProvider in the module home directory
                $moduleHomeFiles = scandir($directory.DIRECTORY_SEPARATOR.$module);
                foreach ($moduleHomeFiles as $file) {
                    //Check if there is a file that contains ControllerProvider in his name
                    if (strpos($file, 'ControllerProvider') != false) {
                        //Found file $file
                        $controllerProviderClassName = substr($file, 0, strpos($file, ".php"));
                        array_push($retrievedControllerProviders, array(
                            'moduleName' => $module,
                            'controllerProviderName' => '\App\Admin\Module\\'.$module.'\\'.$controllerProviderClassName
                        ));
                    }
                }
            }
        }
        return $retrievedControllerProviders;
    }

}