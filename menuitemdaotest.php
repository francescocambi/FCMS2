<?php

error_reporting(E_ALL);
function __autoload($class)
{
    $parts = explode('\\', $class);
	$path = "php";
	foreach ($parts as $part)
		$path .= "/".$part;
	$path .= ".php";
	require_once($path);
}
$menuitemdao = new dao\MenuItemDao();
print_r($menuitemdao->getRootsForMenuId(1));

?>