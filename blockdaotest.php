<?php

error_reporting(E_ERROR);
function __autoload($class)
{
    $parts = explode('\\', $class);
	$path = "php";
	foreach ($parts as $part)
		$path .= "/".$part;
	$path .= ".php";
	require_once($path);
}
$blockdao = new dao\BlockDao();
$blocks = array();
array_push($blocks, $blockdao->getById(1));
array_push($blocks, $blockdao->getById(2));

print_r($blocks);
echo "<br><br>\n\n";

foreach ($blocks as $b) {
	echo $b->getHTML(new Language());
	echo "<br>\n";
}

?>