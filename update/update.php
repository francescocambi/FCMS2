<?php
/**
 * User: Francesco
 * Date: 24/03/15
 * Time: 11:15
 */

//Take fcms root directory
$root = dirname(__DIR__);

//Prepares a list of working folders
$workingFolders = array(
    'root' => $root,
    'apps' => $root.DIRECTORY_SEPARATOR.'apps',
    'config' => $root.DIRECTORY_SEPARATOR.'config',
    'log' => $root.DIRECTORY_SEPARATOR.'log',
    'php' => $root.DIRECTORY_SEPARATOR.'php',
    'plugins' => $root.DIRECTORY_SEPARATOR.'plugins',
    'resources' => $root.DIRECTORY_SEPARATOR.'resources',
    'temp' => $root.DIRECTORY_SEPARATOR.'temp',
    'views' => $root.DIRECTORY_SEPARATOR.'views'
);

//Checks permissions
$result = false;
foreach ($workingFolders as $name => $folder) {
    $result &= is_writable($folder);
    echo str_pad($name, 10)."\t>> ".( (is_writable($folder))?'Ok':'!!' )."\n";
}

if (!$result)
    exit("Some folders are not writable.\nI can't complete the update safely.");

