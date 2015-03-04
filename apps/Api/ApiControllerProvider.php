<?php
/**
 * User: Francesco
 * Date: 24/02/15
 * Time: 17:31
 */

namespace App\Api;


use Silex\Application;
use Silex\ControllerProviderInterface;

class ApiControllerProvider implements ControllerProviderInterface {

    public function connect(Application $app) {
        /**
         * @var ControllerCollection $controllers
         */
        $controllers = $app['controllers_factory'];


        $controllers->get('/module', function () use ($app) {
            /**
             * @var \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
             */
            $token = $app['security']->getToken();
            $userRoles = $token->getRoles();

            $userRolesString = array();
            /** @var \Symfony\Component\Security\Core\Role\Role $role */
            foreach ($userRoles as $role) {
                array_push($userRolesString, $role->getRole());
            }

            /** @var EntityManager $em */
            $em = $app['em'];
            $qb = $em->createQueryBuilder();
            $query = $qb->select('mod')
                ->from('App\Admin\Model\Module', 'mod')
                ->innerJoin('mod.allowedRoles', 'role')
                ->where(
                    $qb->expr()->in('role.name', $userRolesString)
                )->getQuery();

            /** @var \App\Admin\Model\Module[] $modules */
            $modules = $query->execute();

            $modres = array();
            foreach ($modules as $module) {
                array_push($modres, array(
                    'id' => $module->getId(),
                    'name' => $module->getName(),
                    'description' => $module->getDescription(),
                    'routeName' => $module->getRouteName()
                ));
            }

            return json_encode($modres);

        });


        return $controllers;
    }

} 