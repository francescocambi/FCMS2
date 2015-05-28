<?php

namespace Core;

use Doctrine\ORM\EntityManager;

class EntityManagerFactory
{

    private static function prepareEntityFoldersPaths($app) {
        $paths = $app['config']->get('EntityFolders');

        for ($i = 0; $i < count($paths); $i++)
            $paths[$i] = $app['rootFolderPath'].$paths[$i];

        return $paths;

    }

    static function initializeEntityManager($app, $dbParams, $applicationMode = "production")
    {
        $caching = $app['config']->get('Doctrine.caching');

        if ($applicationMode != "development") {
            if ($caching == "xcache")
                $cache = new \Doctrine\Common\Cache\XcacheCache();
        }

        if (!isset($cache)) {
            $cache = new \Doctrine\Common\Cache\ArrayCache();
        }

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        //TODO Load this paths from a config file.
        //This file will be fed from manifest files of varius plugins
        //during plugin setup phase
        $driverImpl = $config->newDefaultAnnotationDriver(EntityManagerFactory::prepareEntityFoldersPaths($app));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(rtrim($app['rootFolderPath']).DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR);
        $config->setProxyNamespace('FCMS2\DoctrineProxy');

        if ($applicationMode == "development") {
            $config->setAutoGenerateProxyClasses(true);
        } else {
            $config->setAutoGenerateProxyClasses(false);
        }

        $em = EntityManager::create($dbParams, $config);

        return $em;
    }

    static function initializeTestEntityManager($app, $applicationMode = "development")
    {

        $cache = new \Doctrine\Common\Cache\ArrayCache();

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        //TODO Load this paths from a config file.
        //This file will be fed from manifest files of varius plugins
        //during plugin setup phase
        $driverImpl = $config->newDefaultAnnotationDriver(EntityManagerFactory::prepareEntityFoldersPaths($app));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($app['rootFolderPath'] . "temp/");
        $config->setProxyNamespace('FCMS2\DoctrineProxy');

        $config->setAutoGenerateProxyClasses(true);

        $em = EntityManager::create(array(
            'driver' => 'pdo_sqlite',
            'memory' => 'true'
        ), $config);

        return $em;
    }

    static function initializeDevelopmentEntityManager($app, $applicationMode = "development")
    {
        $cache = new \Doctrine\Common\Cache\ArrayCache();

        $config = new \Doctrine\ORM\Configuration();
        $config->setMetadataCacheImpl($cache);
        //TODO Load this paths from a config file.
        //This file will be fed from manifest files of varius plugins
        //during plugin setup phase
        $driverImpl = $config->newDefaultAnnotationDriver(EntityManagerFactory::prepareEntityFoldersPaths($app));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir($app['rootFolderPath'] . "temp/");
        $config->setProxyNamespace('FCMS2\DoctrineProxy');

        $config->setAutoGenerateProxyClasses(true);

        $em = EntityManager::create(array(
            'driver' => 'pdo_sqlite',
            'path' => '/Users/Francesco/PhpStormProjects/FCMS2/framework_doc.sqlite'
        ), $config);

        return $em;
    }
}