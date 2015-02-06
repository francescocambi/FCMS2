<?php

require_once("vendor/autoload.php");

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;


/**
 *
 */
//function initializeEntityManager($relativePathToRoot) {
//    $paths = array($relativePathToRoot."php/Model/");
//    $proxydir = $relativePathToRoot."temp/";
//    $isDevMode = false;
//
//    require($relativePathToRoot."connection.properties.php");
//
//    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxydir);
//    // echo "Annotation loaded<br>";
//    $em = EntityManager::create($dbParams, $config);
//    // echo "Entity Manager created!";
//    return $em;
//}

function initializeEntityManager($relativePathToRoot) {
    $applicationMode = "production";
    if ($applicationMode == "development") {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
    } else {
        $cache = new \Doctrine\Common\Cache\XcacheCache();
    }

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl($cache);
    $driverImpl = $config->newDefaultAnnotationDriver($relativePathToRoot."php/Model/");
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($relativePathToRoot."temp/");
    $config->setProxyNamespace('FCMS2\Proxies');

//    if ($applicationMode == "development") {
    if (true) {
        $config->setAutoGenerateProxyClasses(true);
    } else {
        $config->setAutoGenerateProxyClasses(false);
    }

    require($relativePathToRoot."connection.properties.php");

    $em = EntityManager::create($dbParams, $config);

    return $em;
}