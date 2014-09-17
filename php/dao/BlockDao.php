<?php

namespace dao;

class BlockDao {
	
	private $pdo;
		
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	public function getById($id) {
		$row = null;
		
		$sql = "SELECT ID, NAME, DESCRIPTION, BLOCK_STYLE_ID, BG_URL, BG_RED, BG_GREEN, BG_BLUE, BG_OPACITY, BG_REPEATX, BG_REPEATY, BG_SIZE, CONTENT
		FROM BLOCK NATURAL JOIN BLOCK_CONTENT
		WHERE BLOCK.ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		if ($statement->rowCount() > 0)
			$result = $this->makeContentBlockObject($statement->fetch());
		else {
			$sql = "SELECT ID, NAME, DESCRIPTION, BLOCK_STYLE_ID, CODE
			FROM BLOCK NATURAL JOIN BLOCK_FUNCTIONALITY
			WHERE BLOCK.ID=:id";
			$statement = $this->pdo->prepare($sql);
			$statement->bindParam("id", $id);
			$statement->execute();
			if ($statement->rowCount() > 0)
				$result = $this->makeFunctionalityBlockObject($statement->fetch());
		}
		
		return $result;
	}
	
	private function getBlockLanguages($blockid) {
		$sql = "SELECT LANGUAGE_ID FROM BLOCK_LANG WHERE BLOCK_ID=:id";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("id", $id);
		$statement->execute();
		$languages = array();
		$languagedao = new LanguageDao();
		foreach ($statement->fetchAll() as $row) {
			array_push($languages, $languagedao->getById($row['LANGUAGE_ID']));
		}
		return $languages;
	}

	private function makeContentBlockObject($row) {
		$languages = $this->getBlockLanguages($row['ID']);
		$block = new \ContentBlock($row['ID'], $row['NAME'], $row['DESCRIPTION'], $row['CONTENT'], $languages, 
								   $row['BG_URL'], $row['BG_RED'], $row['BG_GREEN'], $row['BG_BLUE'], $row['BG_OPACITY'],
								   $row['BG_REPEATX'], $row['BG_REPEATY'], $row['BG_SIZE']);
		$blockstyledao = new BlockStyleDao();
		$block->setBlockStyle($blockstyledao->getById($row['BLOCK_STYLE_ID']));
		return $block;
	}
	
	private function makeFunctionalityBlockObject($row) {
		$intcaptionsdao = new InternationalCaptionDao();
		$internationalCaptions = $intcaptionsdao->getByBlockId($row['ID']);
		$block = new \FunctionalityBlock($row['ID'], $row['NAME'], $row['DESCRIPTION'], $row['CODE'], $internationalCaptions);
		$blockstyledao = new BlockStyleDao();
		$block->setBlockStyle($blockstyledao->getById($row['BLOCK_STYLE_ID']));
		return $block;
	}
	
}
