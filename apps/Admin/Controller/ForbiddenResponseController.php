<?php
/**
 * User: Francesco
 * Date: 16/04/15
 * Time: 13:13
 */

namespace App\Admin\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ForbiddenResponseController {

    public function render(Application $app) {

        $context = array(
            'menuBlock' => $app['admin.menu']->renderMenu("")
        );

        return new Response($app['twig']->render('App\Admin\Forbidden.twig', $context), 403);
    }

} 