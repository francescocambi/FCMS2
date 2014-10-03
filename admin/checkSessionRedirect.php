<?php

$session = \Authentication\SessionFactory::getInstance()->detectCurrentSession($em, new \Authentication\HttpSessionHandler());

if (is_null($session) || !$session->isValid($_SERVER['REMOTE_ADDR'])) {
    //header("Location: ./", null, 403);
    header("Location: ./");
    exit();
}

?>