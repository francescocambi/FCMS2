<?php
namespace dao;

class PageDao {
	
	private $pdo;
	
	public function __construct() {
		$this->pdo = \Connection::getPDO();
	}
	
	/*
	 * @returns Page
	 */
	public function getById($id) {
		$sql = "SELECT ID, NAME, TITLE, PUBLISHED, PUBLIC, LANGUAGE_ID FROM PAGE WHERE ID=:id";
		
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('id', $id);
		$resultset = $statement->execute();
		$row = $resultset->fetch();
		
		if ($row == null) return null;
		
		return $this->makePageObject($row);
	}
	
	/*
	 * @returns Page
	 */
	public function getByName($name) {
		$sql = "SELECT ID, NAME, TITLE, PUBLISHED, PUBLIC, LANGUAGE_ID FROM PAGE WHERE NAME=:name";
		
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('name', $name);
		try { //DEBUG FIXME
			$statement->execute();
		} catch (PDOException $e) {
			print_r($e);
			exit();
		}
		$row = $statement->fetch();
		
		if ($row == null)
			return null;
		else
			return $this->makePageObject($row);
	}
	
	/*
	 * @returns Page
	 */
	public function getByURL($url) {
		$sql = "SELECT PAGE.ID, NAME, TITLE, PUBLISHED, PUBLIC, LANGUAGE_ID FROM PAGE JOIN URL ON URL.PAGE_ID=PAGE.ID WHERE URL=:url";
		
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('url', $url);
		$statement->execute();
		$row = $statement->fetch();
		
		if ($row == null)
			return null;
		else
			return $this->makePageObject($row);
	}
	
	private function getBlocksForPageId($pageid) {
		$sql = "SELECT BLOCK_ID FROM PAGE_BLOCK WHERE PAGE_ID=:pageid ORDER BY VIEWORDER";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam("pageid", $pageid);
		$statement->execute();
		$result = array();
		$blockdao = new BlockDao();
		foreach ($statement->fetchAll() as $row) {
			array_push($result, $blockdao->getById($row['BLOCK_ID']));
		}
		return $result;
	}
	
	private function getGroupsForPageId($pageid) {
		$sql = "SELECT GROUPS_ID FROM PAG_GRP WHERE PAGE_ID=:pageid";
		$statement = $this->pdo->prepare($sql);
		$statement->bindParam('pageid', $pageid);
		$statement->execute();
		$objs = array();
		$groupdao = new GroupDao();
		foreach ($statement->fetchAll() as $row) {
			array_push($objs, $groupdao->getById($row['GROUP_ID']));
		}
		return $objs;
	}
	
	/* Taken a page table full row, it returns a page object with all details filled 
	 * @param row A full row of page table
	 * @returns a page object based on the row with all details filled
	 * */
	private function makePageObject($row) {
		$published = ($row['PUBLISHED'] == 1);
		$public = ($row['PUBLIC'] == 1);
		if (!$public) {
			$groupdao = new GroupDao();
			$authGroups = $this->getGroupsForPageId($row['ID']);
		} else
			$authGroups = null;
		
		$languagedao = new LanguageDao();
		$language = $languagedao->getById($row['LANGUAGE_ID']);
		
		$blocks = $this->getBlocksForPageId($row['ID']);
		
		return new \Page($row['ID'], $row['NAME'], $row['TITLE'], $published, $public, $language, $blocks, $authGroups);
	}
	
}
