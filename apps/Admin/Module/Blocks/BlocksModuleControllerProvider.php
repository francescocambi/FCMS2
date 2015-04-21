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
            ->bind("admin.blocks.listBlocks")
            ->before($app['moduleAuthorization.check']('Blocks', 'listBlocks'));

        $controllers->match('/edit', '\App\Admin\Module\Blocks\EditorController::renderEditor')
            ->bind("admin.blocks.editNewBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'editNew'));

        $controllers->match('/{id}/edit', '\App\Admin\Module\Blocks\EditorController::renderEditor')
            ->bind("admin.blocks.editBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'edit'));

        $controllers->match('/{id}/delete', '\App\Admin\Module\Blocks\BlocksController::deleteBlock')
            ->bind("admin.blocks.deleteBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'delete'));

        $controllers->match('/checkBlockName', '\App\Admin\Module\Blocks\BlocksController::checkNameUnique')
            ->bind("admin.blocks.checkNameUnique")
            ->before($app['moduleAuthorization.check']('Blocks', 'checkName'));

        $controllers->match('/save', '\App\Admin\Module\Blocks\EditorController::insertBlock')
            ->bind("admin.blocks.insertBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'insert'));

        $controllers->match('/{id}/save', '\App\Admin\Module\Blocks\EditorController::updateBlock')
            ->bind("admin.blocks.updateBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'update'));

        $controllers->match('/{id}', '\App\Admin\Module\Blocks\BlocksController::getBlock')
            ->bind("admin.blocks.getBlock")
            ->before($app['moduleAuthorization.check']('Blocks', 'getBlockContent'));

        return $controllers;

    }

} 