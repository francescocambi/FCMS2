<?php

namespace dao;

class MenuDao {
	
	private $pdo;
	
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getById($id) {
		$sql = "SELECT ID, NAME, DESCRIPTION FROM MENU WHERE ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		$row = $statement->fetch();
		return $this->makeMenuObject($row);
	}
	
	private function makeMenuObject($row) {
		
		//Retrieve menu items for this menu
		$menuitemdao = new MenuItemDao();
		$items = $menuitemdao->getRootsForMenuId($row['ID']);
		
		return new \Menu($row['ID'], $row['NAME'], $row['DESCRIPTION'], $items);
	}
}

?>