<?php

require_once("vendor/autoload.php");

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 *
 */
function initializeEntityManager($relativePathToRoot) {
    $paths = array($relativePathToRoot."php/Model/");
    $isDevMode = true;

    require($relativePathToRoot."connection.properties.php");

    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
    return EntityManager::create($dbParams, $config);
}
