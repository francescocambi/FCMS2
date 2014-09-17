<?php

namespace dao;

class MenuItemDao {
	
	private $pdo;
	
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getById($id) {
		$sql = "SELECT ID, LABEL, URL, ITEM_ORDER, HIDDEN, MENU_ITEM_ID FROM MENU_ITEM WHERE ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		$row = $statement->fetch();
		return $this->makeMenuItemObject($row);
	}
	
	public function getRootsForMenuId($menuid) {
		$sql = "SELECT ID, LABEL, URL, ITEM_ORDER, HIDDEN, MENU_ITEM_ID FROM MENU_ITEM WHERE MENU_ID=:menuid AND MENU_ITEM_ID IS NULL ORDER BY ITEM_ORDER";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("menuid", $menuid);
		$statement->execute();
		$children = array();
		foreach ($statement->fetchAll() as $row) {
			array_push($children, $this->makeMenuItemObject($row));
		}
		return $children;
	}
	
	public function getChildrenForItem($parentid) {
		$sql = "SELECT ID, LABEL, URL, ITEM_ORDER, HIDDEN, MENU_ITEM_ID FROM MENU_ITEM
		WHERE MENU_ID=(SELECT MENU_ID FROM MENU_ITEM WHERE ID=:parentid) AND MENU_ITEM_ID=:parentid ORDER BY ITEM_ORDER";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("parentid", $parentid);
		$statement->execute();
		$children = array();
		foreach ($statement->fetchAll() as $row) {
			array_push($children, $this->makeMenuItemObject($row));
		}
		return $children;
	}
	
	private function makeMenuItemObject($row) {
		//Retrieve children items
		$children = $this->getChildrenForItem($row['ID']);
		$hidden = ($row['HIDDEN'] == 1);
		
		return new \MenuItem($row['ID'], $row['LABEL'], $row['URL'], $row['ITEM_ORDER'], $hidden, $children);
	}
	
}

?>