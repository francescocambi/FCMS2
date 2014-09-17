<?php

class Group {
	
	private $id;
	private $name;
	private $description;
	
	public function __construct($id, $name, $description) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
	}
	
	public function getName() {
		return $name;
	}
	
	public function getDescription() {
		return $description;
	}
	
	/*
	 * Compare this with another Group object.
	 * @param Group
	 * @returns boolean
	 */
	public function equals(Group $group) {
		return ($this->id == $group->id);
	}
	
}

?>