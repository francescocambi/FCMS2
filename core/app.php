<?php
/**
 * User: Francesco
 * Date: 01/02/15
 * Time: 17:04
 */

require_once dirname(__DIR__)."/vendor/autoload.php";

use \Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app = new Silex\Application();

$app['rootFolderPath'] = dirname(__DIR__);

//Register Configuration Files Service
//Loads repository of configuration parameters from specified files
$app->register(new \Yosymfony\Silex\ConfigServiceProvider\ConfigServiceProvider(array(
    $app['rootFolderPath']."/config/"
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
//    return initializeEntityManager("./", $app['config']->get('Database'), $applicationMode);
    return \Core\EntityManagerFactory::initializeDevelopmentEntityManager($app);
});

$monologConf = $app['config']->get('Log');

//Register Monolog Logging Service
$app->register(new \Core\RotatingHandlerMonologServiceProvider(), array(
    'monolog.logfile' => $app['rootFolderPath'].'/log/'.$monologConf['filename'],
    'monolog.maxfiles' => $monologConf['maxfiles'],
    'monolog.level' => $monologConf['level'],
    'monolog.name' => $monologConf['name']
));

$app['monolog'] = $app->share($app->extend('monolog', function($monolog, $app) {

    /**
     * @var Monolog\Logger $monolog
     */
    $monolog->pushProcessor(new \Monolog\Processor\IntrospectionProcessor(\Monolog\Logger::ERROR));

    return $monolog;
}));

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
        'cache' => $app['rootFolderPath'].'/temp/',
        'auto_reload' => true,
        'autoescape' => false
    );
}
$app->register(new \Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => $twig_options,
    'twig.loader' => new \Core\CustomTwigLoader($app['rootFolderPath'])
));

/** @var Twig_Environment $twig */
$twig = $app['twig'];

if ($app['config']->get('MaintenanceMode')) {
    $pattern = '^/(?:(?!login))';
} else {
    $pattern = '^/admin|api';
}

//Set password encoder
$app['password_encoder'] = $app->share(function () {
    return new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
});

//Register Security Service
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
//    'security.encoder.digest' => new \Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder(),
    'security.encoder.digest' => $app['password_encoder'],
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => $pattern,
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

//$app['session.db_options'] = array(
//    'db_table' => 'Session',
//    'db_id_col' => 'id',
//    'db_data_col' => 'value',
//    'db_lifetime_col' => 'lifetime',
//    'db_time_col' => 'time'
//);
//
//$app['session.storage.handler'] = $app->share(function () use ($app) {
//    return new \Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler(
//        $app['em']->getConnection()->getWrappedConnection(),
//        $app['session.db_options'],
//        $app['session.storage.options']
//    );
//});

//Routes definition

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render('\App\Admin\Login.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
})->bind('login');

$app->match('/admin', function () use ($app) {
    return $app->redirect('/admin/');
});

$app->mount('/', new \App\Site\SiteControllerProvider());

$app->mount('/admin', new \App\Admin\AdminControllerProvider());


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