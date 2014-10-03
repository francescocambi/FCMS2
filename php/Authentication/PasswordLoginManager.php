<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 13.05
 */

namespace Authentication;

use \Model\Session;

class PasswordLoginManager implements LoginManager {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param $em \Doctrine\ORM\EntityManager
     */
    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * Process login with data passed as argument
     * array("email" => mail, "password" => pass)
     * @param array $params
     * @return Session
     */
    public function doLogin($params) {
        $user = $this->em->getRepository('Model\User')
            ->findOneBy(array(
                "email" => $params['email'],
                "password" => $params['password']
            ));
        if (is_null($user)) return null;

        return SessionFactory::getInstance()->createNewSession($this->em, new HttpSessionHandler(), $user);
    }

} 