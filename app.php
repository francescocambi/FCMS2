<?php
/**
 * User: Francesco
 * Date: 01/02/15
 * Time: 17:04
 */

require_once __DIR__.'/bootstrap.php';

use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();

$app['debug'] = true;

$app['em'] = $app->share(function () {
    return initializeEntityManager("./");
});

$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => array(
        __DIR__.'/views/',
        __DIR__.'/apps/Site/views/'
    ),
    'twig.options' => array(
        'cache' => __DIR__.'/temp/',
        'auto_reload' => true,
        'autoescape' => false
    )
));

$app['page.controller'] = $app->share(function () use ($app) {
    return new \App\Site\Controller\SiteController();
});

$app->match('/{lang}/', 'page.controller:renderPage')
    ->assert('lang', '[a-z]{2}')
    ->bind('languageOnly');

$app->match('/{url}', 'page.controller:renderPage')
    ->assert('url', '[a-z\-]{3}[a-z\-]+')
    ->value('url', 'home_it')
    ->bind('urlOnly');

$app->match('/{lang}/{url}', 'page.controller:renderPage')
    ->assert('lang','[a-z]{2}')
    ->bind('languageAndUrl');

//$app->error(function (\Exception $e, $code) use ($app) {
//    switch ($code) {
//        case 404:
//            $message = $app['twig']->render('NotFound.twig', array(
//                'message' => $e->getMessage(),
//            ));
//            break;
//    }
//
//    return new Response($message);
//});


$app->run();
