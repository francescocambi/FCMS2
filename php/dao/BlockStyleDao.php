<?php

namespace dao;

class BlockStyleDao {
	
	private $pdo;
	
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getById($id) {
		$sql = "SELECT CLASSNAME FROM BLOCK_STYLE WHERE ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		return $this->makeBlockStyleObject($statement->fetch());
	}
	
	private function makeBlockStyleObject($row) {
		return new $row['CLASSNAME']();	
	}
	
}
