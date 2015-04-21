<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 15:27
 */

namespace App\Admin\Menu;

use App\Admin\Model\Module;
use Doctrine\ORM\EntityManager;
use Silex\Application;

class Menu {

    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Extracts modules for which the logged in user is allowed
     * @param string $activeModuleName Name of the active module (currently displayed)
     * @return string
     */
    public function renderMenu($activeModuleName=null) {

        /*
         * Token is of type
         * Symfony\Component\Security\Core\Authentication\Token\TokenInterface
         */
        /**
         * @var \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token
         */
        $token = $this->app['security']->getToken();
        $userRoles = $token->getRoles();

        $userRolesString = array();
        /** @var \Symfony\Component\Security\Core\Role\Role $role */
        foreach ($userRoles as $role) {
            array_push($userRolesString, $role->getRole());
        }

        /** @var EntityManager $em */
        $em = $this->app['em'];
        $qb = $em->createQueryBuilder();
        $query = $qb->select('mod')
            ->from('App\Admin\Model\Module', 'mod')
            ->innerJoin('mod.allowedRoles', 'role')
            ->where(
                $qb->expr()->in('role.name', $userRolesString)
            )->orderBy($qb->expr()->asc('mod.menuOrder'))->getQuery();

        /** @var Module[] $modules */
        $modules = $query->execute();


        return $this->app['twig']->render('App\Admin\AdminMenu.twig', array(
           'modules' => $modules,
            'activeModuleName' => $activeModuleName,
            'userDisplayName' => $token->getUsername()
        ));

    }

} 