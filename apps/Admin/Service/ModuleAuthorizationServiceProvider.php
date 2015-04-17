<?php
/**
 * User: Francesco
 * Date: 16/04/15
 * Time: 12:34
 */

namespace App\Admin\Service;


use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Class ModuleAuthorizationServiceProvider
 *
 *
 *
 * @package App\Admin\Service
 */
class ModuleAuthorizationServiceProvider implements ServiceProviderInterface {

    const AUTHORIZER_KEY = "moduleAuthorization.authorizer";
    const CONTROLLER_KEY = "moduleAuthroization.forbiddenResponseController";
    const CHECK_FN_KEY = "moduleAuthorization.check";

    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app[self::CHECK_FN_KEY] = $app->protect(function ($moduleName, $actionId) use ($app) {
            return function ($request) use ($app, $moduleName, $actionId) {

                $auth = $app[self::AUTHORIZER_KEY];
                $forbiddenResponseController = $app[self::CONTROLLER_KEY];

                return $auth->checkAuthorization($moduleName, $forbiddenResponseController);
            };
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {

        if (!isset($app[self::AUTHORIZER_KEY]) || is_null($app[self::AUTHORIZER_KEY])) {
            throw new \InvalidArgumentException("You must provide a ModuleAuthorization object for
             argument \"moduleAuthorization.authorizer\" registering ModuleAuthorizationServiceProvider");
        }

        if (!isset($app[self::CONTROLLER_KEY]) || is_null($app[self::CONTROLLER_KEY])) {
            throw new \InvalidArgumentException("You must provide a Controller object for
             argument \"moduleAuthorization.forbiddenResponseController\" registering ModuleAuthorizationServiceProvider");
        }

    }


} 