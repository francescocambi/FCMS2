<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 13.19
 */

namespace Authentication;

use Model\Session;
use Model\User;
use \DateTime;

class SessionFactory {

    private static $instance;

    private $TOKEN_LENGHT = 30;
    private $TOKEN_DOMAIN = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
        's', 't', 'u', 'v','w','x','y', 'z','A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R','S','T','U','V','W','X','Y','Z','1','2','3','4','5','6','7','8','9','0');

    private function __construct() {

    }

    /**
     * @return SessionFactory
     */
    public static function getInstance() {
        if (is_null(SessionFactory::$instance))
            SessionFactory::$instance = new SessionFactory();
        return SessionFactory::$instance;
    }

    /**
     * @param SessionHandler $handler
     * @param \Doctrine\ORM\EntityManager $em
     * @return Session
     */
    public function detectCurrentSession($em, SessionHandler $handler) {
        $token = $handler->getIdentifierToken();

        return $em->getRepository('Model\Session')->findOneBy(array("token" => $token));
    }

    /**
     * Initialize and returns a new Session for the user
     * @param \Doctrine\ORM\EntityManager $em
     * @param SessionHandler $handler
     * @param User $user
     * @return Session
     */
    public function createNewSession($em, SessionHandler $handler, User $user) {
        $loginTime = new DateTime();
        //Create Session
        $session = new Session();
        $session->setLoginTimestamp($loginTime);
        $session->setUser($user);
        $session->setClientIpAddress($_SERVER['REMOTE_ADDR']);
        $token = $this->generateToken($loginTime);
        $session->setToken($token);

        //Persist Session
        $em->persist($session);
        $em->flush();

        //Open session on handler
        $handler->openSession($token);

        return $session;
    }

    /**
     * Generate random string and encrypt it to use as session identifier (token)
     * @param $loginTime DateTime
     * @return string
     */
    private function generateToken($loginTime) {
        //Token generation
        $token = "";
        for ($i=0; $i<$this->TOKEN_LENGHT; $i++) {
            $token .= $this->TOKEN_DOMAIN[rand(0, count($this->TOKEN_DOMAIN))];
        }

        $token = sha1($token.$loginTime->getTimestamp());

        return $token;
    }

} 