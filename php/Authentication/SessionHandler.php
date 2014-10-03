<?php
/**
 * User: Francesco
 * Date: 02/10/14
 * Time: 15.06
 */

namespace Authentication;

interface SessionHandler {

    public function getIdentifierToken();

    public function openSession($identifier);

    public function closeSession();

} 