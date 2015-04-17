<?php
/**
 * User: Francesco
 * Date: 11/02/15
 * Time: 12:06
 */

namespace App\Admin;


use Doctrine\ORM\EntityManager;
use Silex\Application;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface {

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Application
     */
    private $app;

    /**
     * @param EntityManager $em
     * @param Application $app
     */
    public function __construct(EntityManager $em, Application $app) {
        $this->entityManager = $em;
        $this->app = $app;
    }

    /**
     * {{@inheritdoc}}
     */
    public function loadUserByUsername($username) {
        /** @var \Model\User $user */
        $user = $this->entityManager->getRepository('Model\User')->findOneBy(array(
            'username' => $username
        ));

        if (is_null($user)) {
            $exception = new UsernameNotFoundException();
            $exception->setUsername($username);
            throw $exception;
        }

        return $user;
    }

    /**
     * {{@inheritdoc}}
     */
    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {{@inheritdoc}}
     */
    public function supportsClass($class) {
        return $class === 'Symfony\Component\Security\Core\User\User'
            || $class === 'Model\User';
    }

} 