<?php
/**
 * User: Francesco
 * Date: 17/02/15
 * Time: 11:58
 */

namespace App\Admin\Controller;


use App\Admin\Menu\Menu;
use Silex\Application;

class AdminFrontController {

    /**
     * @param Application $app
     * @param string $requestedUrl
     * @return string
     */
    public function render(Application $app, $requestedUrl) {

        $menuController = new Menu($app);
        $menuBlock = $menuController->renderMenu($requestedUrl);

        return $app['twig']->render('HomeModule.twig', array(
            'menuBlock' => $menuBlock
        ));

    }

} 