<?php

class Menu implements HierarchicalMenu {
	
	private $id;
	private $name;
	private $description;
	private $items = array(); //Array of MenuItems objects
	
	public function __construct($id, $name, $description, $items) {
		$this->id = $id;
		$this->name = $name;
		$this->description = $description;
		$this->items = $items;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getChildren() {
		return $this->items;
	}
	
}

?>