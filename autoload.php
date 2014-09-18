
<?php
/**
 * User: Francesco
 * Date: 17/09/14
 * Time: 15.25
 */

function __autoload($class)
{
    $parts = explode('\\', $class);
    $path = "php";
    foreach ($parts as $part)
        $path .= "/".$part;
    $path .= ".php";
    require_once($path);
}

spl_autoload_register("__autoload");

?>
