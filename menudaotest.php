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
$menudao = new dao\MenuDao();
$menu = $menudao->getById(1);

print_r($menu);

?>