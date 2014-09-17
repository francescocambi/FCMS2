<?php

class MenuItem implements HierarchicalMenu {
	
	private $id;
	private $label;
	private $url;
	private $order;
	private $hidden;
	private $children; //Array of MenuItem objects
	
	public function __construct($id, $label, $url, $order, $hidden, $children) {
		$this->id = $id;
		$this->label = $label;
		$this->url = $url;
		$this->order = $order;
		$this->hidden = $hidden;
		$this->children = $children;
	}
	
	public function getLabel() {
		return $this->label;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function isHidden() {
		return $this->isHidden();
	}
	
	public function getChildren() {
		return $this->children;
	}
	
}
?>