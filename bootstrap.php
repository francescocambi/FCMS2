<?php

require_once("vendor/autoload.php");

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 *
 */
function initializeEntityManager($relativePathToRoot) {
    $paths = array($relativePathToRoot."php/Model/");
    $proxydir = $relativePathToRoot."temp/";
    $isDevMode = true;

    require($relativePathToRoot."connection.properties.php");

    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxydir);
    // echo "Annotation loaded<br>";
    $em = EntityManager::create($dbParams, $config);
    // echo "Entity Manager created!";
    return $em;
}
