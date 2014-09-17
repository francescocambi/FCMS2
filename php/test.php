<?php

function __autoload($class)
{
    $parts = explode('\\', $class);
	$path = ".";
	foreach ($parts as $part)
		$path .= "/".$part;
	$path .= ".php";
	require_once($path);
}

$dao = new dao\PageDao();
echo $dao->printmessage();

?>
