<?php
/**
 * User: Francesco
 * Date: 28/02/15
 * Time: 19:04
 */

namespace App\Admin\Module\Blocks;


use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class BlocksModuleControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {

        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];

        $controllers->match('/checkBlockName', '\App\Admin\Module\Blocks\BlocksController::checkNameUnique')
            ->bind("admin.blocks.checkNameUnique");

        $controllers->match('/{id}', '\App\Admin\Module\Blocks\BlocksController::getBlock')
            ->bind("admin.blocks.getBlock");

        return $controllers;

    }

} 