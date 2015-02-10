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

//Register Configuration Files Service
//Loads repository of configuration parameters from specified files
$app->register(new \Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider(array(
    __DIR__."/config/"
)));
$app['config'] = $app->share(function () use ($app) {
    return $app['configuration']->load('config.json');
});

$app['debug'] = $app['config']->get('Application.Debug');

if ($app['config']->get('Application.Development')) {
    $applicationMode = "development";
} else {
    $applicationMode = "production";
}

$app['em'] = $app->share(function () use ($applicationMode) {
    return initializeEntityManager("./", $applicationMode);
});

//Register Controller Service
//Dynamically loads controllers for app->match
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

//Register Url Generator Service
//Given a set of parameters it helps generating urls from
//the bind name specified for a route
$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());

//Register Twig Template System Service
//Twig's job is to render views based on specific template files

if ($app['config']->get('Application.Development')) {
    $twig_options = array(
        'autoescape' => false
    );
} else {
    $twig_options = array(
        'cache' => __DIR__.'/temp/',
        'auto_reload' => true,
        'autoescape' => false
    );
}
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => array(
        __DIR__.'/views/',
        __DIR__.'/apps/Site/views/',
        __DIR__.'/plugins/ContactMe/views/'
    ),
    'twig.options' => $twig_options
));

//Define pages front controller
$app['page.controller'] = $app->share(function () use ($app) {
    return new \App\Site\Controller\SiteController();
});

//Routes definition
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

//Application Execution
$app->run();
