<?php
/**
 * User: Francesco
 * Date: 27/03/15
 * Time: 17:31
 */

namespace Core;


use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Handler\DebugHandler;
use Silex\EventListener\LogListener;

class RotatingHandlerMonologServiceProvider implements ServiceProviderInterface {

    public function register(Application $app)
    {
        $app['logger'] = function () use ($app) {
            return $app['monolog'];
        };

        if ($bridge = class_exists('Symfony\Bridge\Monolog\Logger')) {
            $app['monolog.handler.debug'] = function () use ($app) {
                $level = RotatingHandlerMonologServiceProvider::translateLevel($app['monolog.level']);

                return new DebugHandler($level);
            };
        }

        $app['monolog.logger.class'] = $bridge ? 'Symfony\Bridge\Monolog\Logger' : 'Monolog\Logger';

        $app['monolog'] = $app->share(function ($app) {
            $log = new $app['monolog.logger.class']($app['monolog.name']);

            $log->pushHandler($app['monolog.handler']);

            if ($app['debug'] && isset($app['monolog.handler.debug'])) {
                $log->pushHandler($app['monolog.handler.debug']);
            }

            return $log;
        });

        $app['monolog.handler'] = function () use ($app) {
            $level = RotatingHandlerMonologServiceProvider::translateLevel($app['monolog.level']);

            return new RotatingFileHandler($app['monolog.logfile'], $app['monolog.maxfiles'], $level, $app['monolog.bubble'], $app['monolog.permission']);
        };

        $app['monolog.level'] = function () {
            return Logger::DEBUG;
        };

        $app['monolog.listener'] = $app->share(function () use ($app) {
            return new LogListener($app['logger']);
        });

        $app['monolog.name'] = 'myapp';
        $app['monolog.bubble'] = true;
        $app['monolog.permission'] = null;
    }

    public function boot(Application $app)
    {
        if (isset($app['monolog.listener'])) {
            $app['dispatcher']->addSubscriber($app['monolog.listener']);
        }
    }

    public static function translateLevel($name)
    {
        // level is already translated to logger constant, return as-is
        if (is_int($name)) {
            return $name;
        }

        $levels = Logger::getLevels();
        $upper = strtoupper($name);

        if (!isset($levels[$upper])) {
            throw new \InvalidArgumentException("Provided logging level '$name' does not exist. Must be a valid monolog logging level.");
        }

        return $levels[$upper];
    }

} 