<?php

require_once("../php/Connection.php");

//TODO: Verify html session

$pdo = Connection::getPDO();

$sql = "SELECT BLOCK.ID, BLOCK_CONTENT.ID, BLOCK_CONTENT.CONTENT, BLOCK.NAME, BLOCK.DESCRIPTION, BLOCK.BLOCK_STYLE_ID, BG_URL,
			 BG_RED, BG_GREEN, BG_BLUE, BG_OPACITY, BG_REPEATX, BG_REPEATY, BG_SIZE
			 FROM BLOCK NATURAL JOIN BLOCK_CONTENT WHERE BLOCK.ID=:blockid";

$statement = $pdo->prepare($sql);
$statement->bindParam("blockid", $_GET['blockid']);
$statement->execute();

$result_string = "{";
$row = $statement->fetch(PDO::FETCH_ASSOC);

foreach ($row as $key => $value) {
	$result_string .= "\t".json_encode($key).": ".json_encode($value).",\n";
}
$result_string = substr($result_string, 0, strlen($result_string)-2);
$result_string .= "\n}";

echo $result_string;

?>