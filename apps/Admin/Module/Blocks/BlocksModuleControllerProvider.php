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

        $controllers->match('/', '\App\Admin\Module\Blocks\BlocksController::listBlocks')
            ->bind("admin.blocks.listBlocks");

        $controllers->match('/edit', '\App\Admin\Module\Blocks\EditorController::renderEditor')
            ->bind("admin.blocks.editNewBlock");

        $controllers->match('/{id}/edit', '\App\Admin\Module\Blocks\EditorController::renderEditor')
            ->bind("admin.blocks.editBlock");

        $controllers->match('/{id}/delete', '\App\Admin\Module\Blocks\BlocksController::deleteBlock')
            ->bind("admin.blocks.deleteBlock");

        $controllers->match('/checkBlockName', '\App\Admin\Module\Blocks\BlocksController::checkNameUnique')
            ->bind("admin.blocks.checkNameUnique");

        $controllers->match('/save', '\App\Admin\Module\Blocks\EditorController::insertBlock')
            ->bind("admin.blocks.insertBlock");

        $controllers->match('/{id}/save', '\App\Admin\Module\Blocks\EditorController::updateBlock')
            ->bind("admin.blocks.updateBlock");

        $controllers->match('/{id}', '\App\Admin\Module\Blocks\BlocksController::getBlock')
            ->bind("admin.blocks.getBlock");

        return $controllers;

    }

} 