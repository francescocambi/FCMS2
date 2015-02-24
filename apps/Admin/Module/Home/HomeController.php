<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 12:16
 */

namespace App\Admin\Module\Home;


use Silex\Application;

class HomeController {

    public function render(Application $app) {
        return $app['twig']->render('App\Admin\Module\Home\HomeView.twig', array(
            'menuBlock' => $app['admin.menu']->renderMenu('Home'),
            'welcomeMessage' => "Welcome! This is the new Admin Area. If you are reading this, all is working correctly!"
        ));
    }

} 