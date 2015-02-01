<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 15.11
 */

namespace Authentication;

class CookieSessionHandler implements SessionHandler {

    public function getIdentifierToken() {
        return $_COOKIE['fcmssestoken'];
    }

    public function openSession($identifier) {
        setcookie('fcmssestoken', $identifier, time()+3600);
    }

    public function closeSession() {
        setcookie('fcmssestoken', '', 1);
    }

} 