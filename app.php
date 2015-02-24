<?php
/**
 * User: Francesco
 * Date: 01/02/15
 * Time: 17:04
 */

require_once __DIR__.'/bootstrap.php';

require_once 'CustomTwigLoader.php';

use \Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app = new Silex\Application();

//Register Configuration Files Service
//Loads repository of configuration parameters from specified files
$app->register(new \Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider(array(
    __DIR__."/config/"
)));
$app['config'] = $app->share(function () use ($app) {
    return $app['configuration']->load('config.json');
});

// Setting application execution mode
$app['debug'] = $app['config']->get('Application.Debug');

if ($app['config']->get('Application.Development')) {
    $applicationMode = "development";
} else {
    $applicationMode = "production";
}

// Initializing Entity Manager Doctrine ORM
$app['em'] = $app->share(function () use ($app, $applicationMode) {
    return initializeEntityManager("./", $app['config']->get('Database'), $applicationMode);
});

//Register Monolog Logging Service
$app->register(new \Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/log/app.log',
    'monolog.level' => 'DEBUG',
    'monolog.name' => 'application'
));

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
        __DIR__.'/plugins/ContactMe/views/',
        __DIR__.'/apps/Admin/views/'
    ),
    'twig.options' => $twig_options,
    'twig.loader' => new CustomTwigLoader(null)
));

/** @var Twig_Environment $twig */
$twig = $app['twig'];

//Register Security Service
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.encoder.digest' => new \Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder(),
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/admin',
//            'http' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
            'logout' => array('logout_path' => '/admin/logout'),
            'users' => $app->share(function () use ($app) {
                return new \App\Admin\UserProvider($app['em'], $app);
            })
        )
    )
));

//Register Session Service
$app->register(new \Silex\Provider\SessionServiceProvider());



//Define pages front controller
$app['page.controller'] = $app->share(function () use ($app) {
    return new \App\Site\Controller\SiteController();
});

//Routes definition

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('App\Admin\Login.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->mount('/admin', new \App\Admin\AdminControllerProvider());

$app->match('/admin', function () use ($app) {
    return $app->redirect('/admin/');
});

$app->mount('/', new \App\Site\SiteControllerProvider());

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

return $app;
