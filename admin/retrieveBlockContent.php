<?php

require_once("../php/Connection.php");

//TODO: Verify html session

$pdo = Connection::getPDO();

$sql = "SELECT BLOCK.ID, BLOCK_CONTENT.CONTENT FROM BLOCK NATURAL JOIN BLOCK_CONTENT WHERE BLOCK.ID=:blockid";

$statement = $pdo->prepare($sql);
$statement->bindParam("blockid", $_GET['blockid']);
$statement->execute();

foreach ($statement->fetchAll() as $row) {
	echo $row['CONTENT'];
}

?>