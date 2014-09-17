<?php

namespace dao;

class LanguageDao {
	
	private $pdo;
		
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getAll() {
		$sql = "SELECT ID, DESCRIPTION, CODE, FLAG_IMG_URL, MENU_ID FROM LANGUAGES";
		$results = array();
		foreach ($this->pdo->query($sql) as $row) {
			array_push($results, $this->makeLanguageObject($row));
		}
		return $results;		
		
	} 
	
	public function getById($id) {
		$sql = "SELECT ID, DESCRIPTION, CODE, FLAG_IMG_URL, MENU_ID FROM LANGUAGES WHERE ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		$row = $statement->fetch();
		return $this->makeLanguageObject($row);
	}
	
	public function getByCode($code) {
		$sql = "SELECT ID, DESCRIPTION, CODE, FLAG_IMG_URL, MENU_ID FROM LANGUAGES WHERE CODE=:code";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("code", $code);
		$statement->execute();
		$row = $statement->fetch();
		return $this->makeLanguageObject($row);
	}
	
	/* Taken a language table full row, it returns a language object with all details filled 
	 * @param row A full row of language table
	 * @returns a language object based on the row with all details filled
	 * */
	private function makeLanguageObject($row) {
		
		//Retrieving menu object
		$menudao = new MenuDao();
		$menu = $menudao->getById($row['MENU_ID']);
		
		return new \Language($row['ID'], $row['DESCRIPTION'], $row['CODE'], $row['FLAG_IMG_URL'], $menu);
	}
	
}
