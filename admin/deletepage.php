<?php
require_once("../php/Connection.php");

//TODO: Verify html session

$pdo = Connection::getPDO();

if (isset($_POST['pageid']) && $_POST['pageid'] != "") {
	$sql = "DELETE FROM PAGE WHERE ID=?";
	try {
		$statement = $pdo->prepare($sql);
		$statement->execute(array($_POST['pageid']));
		echo "OK";
	} catch (PDOException $e) {
		$pdo->rollback();
		echo "TRACE => ".$e->getTraceAsString();
		exit("\nEXCEPTION: ".$e->getMessage());
	}
	
}
