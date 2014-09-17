<?php

class Page {
	
	private $id;
	private $name;
	private $title;
	private $published;
	private $public;
	private $language;
	private $blocks = array(); //Array di Blocks
	private $authGroups = array(); //Array di Groups
	
	public function __construct($id, $name, $title, $published, $public, $language, $blocks, $authGroups) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->published = $published;
		$this->public = $public;
		$this->language = $language;
		$this->blocks = $blocks;
		$this->authGroups = $authGroups;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function isPublished() {
		return $this->published;
	}
	
	public function isPublic() {
		return $this->public;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
	public function getBlocks() {
		return $this->blocks;
	}
	
	// public function getBlocksForLanguage(Language $lang) {
		// $returnObjects = new ArrayObject();
		// foreach ($this->blocks as $block)
			// if ($block->hasLanguage($lang))
				// $returnObjects->append($block);
		// return $returnObjects->getArrayCopy();
	// }
	
	public function canBeViewedBy(User $userId) {
		
		$userGroups = $userId->getGroups();
		foreach ($authGroups as $group)
			foreach ($userGroups as $userGroup)
				if ($group->equals($userGroup))
					return true;
				
		return false;
		
	}
	
	
	
}

?>