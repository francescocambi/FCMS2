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
$pagedao = new dao\PageDao();
$page = $pagedao->getByName("home");

print_r($page->getBlocks());

echo "<br><br>";

print_r($page);

?>