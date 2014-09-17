<?php

namespace dao;

class InternationalCaptionDao {
	
	private $pdo;
	
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getById($id) {
		
	}
	
	public function getByBlockId($blockid) {
		$sql = "SELECT STRING_KEY, STRING_VALUE, LANGUAGE_ID FROM FUNCT_STRING WHERE BLOCK_FUNCTIONALITY_ID=:blockid";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("blockid", $blockid);
		$statement->execute();
		$result = array();
		foreach ($statement->fetchAll as $row) {
			if (!isset($result[$row['LANGUAGE_ID']])) {
				if (!isset($languagedao)) $languagedao = new LanguageDao();
				$result[$row['LANGUAGE_ID']] = new \InternationalCaption(array(), $languagedao->getById($row['LANGUAGE_ID']));
			}
			$result[$row['LANGUAGE_ID']]->updateCaptionsArray($row['STRING_KEY'], $row['STRING_VALUE']);
		}
		
		return $result;
	}
	
}
