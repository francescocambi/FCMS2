<?php

require_once("vendor/autoload.php");

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

function initializeEntityManager($relativePathToRoot, $dbParams, $applicationMode="production") {
    if ($applicationMode == "development") {
        $cache = new \Doctrine\Common\Cache\ArrayCache;
    } else {
        $cache = new \Doctrine\Common\Cache\XcacheCache();
    }

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl($cache);
    //TODO Load this paths from a config file.
    //This file will be fed from manifest files of varius plugins
    //during plugin setup phase
    $driverImpl = $config->newDefaultAnnotationDriver(array(
        $relativePathToRoot."php/Model/",
        $relativePathToRoot."plugins/ContactMe/Model/",
        $relativePathToRoot."apps/Admin/Model/"
    ));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($relativePathToRoot."temp/");
    $config->setProxyNamespace('FCMS2\DoctrineProxy');

    if ($applicationMode == "development") {
        $config->setAutoGenerateProxyClasses(true);
    } else {
        $config->setAutoGenerateProxyClasses(false);
    }

    $em = EntityManager::create($dbParams, $config);

    return $em;
}

function initializeTestEntityManager($relativePathToRoot, $applicationMode="development") {

    $cache = new \Doctrine\Common\Cache\ArrayCache();

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl($cache);
    //TODO Load this paths from a config file.
    //This file will be fed from manifest files of varius plugins
    //during plugin setup phase
    $driverImpl = $config->newDefaultAnnotationDriver(array(
        $relativePathToRoot."php/Model/",
        $relativePathToRoot."plugins/ContactMe/Model/",
        $relativePathToRoot."apps/Admin/Model/"
    ));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($relativePathToRoot."temp/");
    $config->setProxyNamespace('FCMS2\DoctrineProxy');

    $config->setAutoGenerateProxyClasses(true);

    $em = EntityManager::create(array(
        'driver' => 'pdo_sqlite',
        'memory' => 'true'
    ), $config);

    return $em;
}

function initializeDevelopmentEntityManager($relativePathToRoot, $applicationMode="development") {
    $cache = new \Doctrine\Common\Cache\ArrayCache();

    $config = new \Doctrine\ORM\Configuration();
    $config->setMetadataCacheImpl($cache);
    //TODO Load this paths from a config file.
    //This file will be fed from manifest files of varius plugins
    //during plugin setup phase
    $driverImpl = $config->newDefaultAnnotationDriver(array(
        $relativePathToRoot."php/Model/",
        $relativePathToRoot."plugins/ContactMe/Model/",
        $relativePathToRoot."apps/Admin/Model/"
    ));
    $config->setMetadataDriverImpl($driverImpl);
    $config->setQueryCacheImpl($cache);
    $config->setProxyDir($relativePathToRoot."temp/");
    $config->setProxyNamespace('FCMS2\DoctrineProxy');

    $config->setAutoGenerateProxyClasses(true);

    $em = EntityManager::create(array(
        'driver' => 'pdo_sqlite',
        'path' => 'framework_doc.sqlite'
    ), $config);

    return $em;
}