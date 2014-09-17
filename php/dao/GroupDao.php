<?php

namespace dao;

class GroupDao {
	
	private $pdo;
		
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getAll() {
		$sql = "SELECT ID, NAME, DESCRIPTION FROM GROUP";
		$objs = array();
		foreach ($this->pdo->query($sql) as $row) {
			array_push($objs, $this->makeGroupObject($row));
		}
		return $objs;
	}
	
	public function getById($id) {
		$sql = "SELECT ID, NAME, DESCRIPTION FROM GROUP WHERE ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('id', $id);
		$statement->execute();
		return $this->makeGroupObject($statement->fetch());
	}
	
	//Spostare su pagedao
	public function getByPageId($pageid) {
		$sql = "SELECT ID, NAME, DESCRIPTION FROM GROUP JOIN PAG_GRP ON GROUP.ID=PAG_GRP.GROUP_ID WHERE PAG_GRP.PAGE_ID=:pageid";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('pageid', $pageid);
		$resultset = $statement->execute();
		$groups = $resultset->fetchAll();
		$objs = array();
		foreach ($groups as $row) {
			array_push($objs, $this->makeGroupObject($row));
		}
		return $objs;
	}
	
	/* Taken a group table full row, it returns a group object with all details filled 
	 * @param row A full row of group table
	 * @returns A group object based on the row with all details filled
	 * */
	private function makeGroupObject($row) {
		return new \Group($row['ID'], $row['NAME'], $row['DESCRIPTION']);
	}
	
}
