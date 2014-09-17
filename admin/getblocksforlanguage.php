<?php

require_once("../php/Connection.php");

//TODO: Verify html session

$pdo = Connection::getPDO();

$sql = "SELECT BLOCK.ID, BLOCK.NAME, BLOCK_STYLE.NAME 'STYLE'
		FROM BLOCK, BLOCK_STYLE
		WHERE BLOCK.BLOCK_STYLE_ID = BLOCK_STYLE.ID";

$statement = $pdo->prepare($sql);
$statement->execute();

foreach ($statement->fetchAll() as $row) {
	echo "<tr>";
	echo "<td><input type=\"checkbox\" value=\"".$row['ID']."\"></td>";
	echo "<td>".$row['NAME']."</td>";
	echo "<td>".$row['STYLE']."</td>";
	echo "</tr>";
}

?>