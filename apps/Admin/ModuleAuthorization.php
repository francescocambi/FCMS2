<?php
/**
 * User: Francesco
 * Date: 16/04/15
 * Time: 12:02
 */

namespace App\Admin;


use Silex\Application;

class ModuleAuthorization {

    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $application) {
        $this->app = $application;
    }

    public function checkAuthorization($moduleName, $forbiddenController) {

        //Checks if user is fully authenticated
        if ($this->app['security']->isGranted('IS_AUTHENTICATED_FULLY') === FALSE) {
            //If not redirect to login
            return $this->app->redirect(
                $this->app['url_generator']->generate('login')
            );
        }

        //Checks if user has rights to access requested module

        /** @var \App\Admin\Model\Module $module */
        $module = $this->app['em']->getRepository('\App\Admin\Model\Module')->findOneBy(array(
            'name' => $moduleName
        ));
        $result = 0;
        foreach ($module->getAllowedRoles()->toArray() as $role) {
            $result = $this->app['security']->isGranted($role->getName()) + $result;
        }
        //Result is ok - access granted
        if ($result > 0) return null;

        //User cannot access requested module. Reply with 403.
        $response = $forbiddenController->render($this->app);

        return $response;
    }

} 