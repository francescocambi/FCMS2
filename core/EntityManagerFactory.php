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
        //Load this paths from config file.
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
        //Load this paths from config file
        $driverImpl = $config->newDefaultAnnotationDriver(EntityManagerFactory::prepareEntityFoldersPaths($app));
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir(rtrim($app['rootFolderPath']).DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR);
        $config->setProxyNamespace('FCMS2\DoctrineProxy');

        $config->setAutoGenerateProxyClasses(true);

        $em = EntityManager::create(array(
            'driver' => 'pdo_sqlite',
            'memory' => 'true'
        ), $config);

        return $em;
    }
}