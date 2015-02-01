<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 15.32
 */

namespace Authentication;

class HttpSessionHandler implements SessionHandler {

    public function getIdentifierToken() {
        session_start();
        return $_SESSION['fcmssestoken'];
    }

    public function openSession($identifier) {
        session_start();
        $_SESSION['fcmssestoken'] = $identifier;
    }

    public function closeSession() {
        session_start();
        $_SESSION['fcmssestoken'] = "";
        session_destroy();
    }

} 