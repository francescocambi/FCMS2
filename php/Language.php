<?php

class Language {
	
	public $id;
	public $description;
	public $flagImageURL;
	public $code;
	public $menu; //Menu object
	
	public function __construct($id, $description, $code, $flagImageURL, $menu) {
		$this->id = $id;
		$this->description = $description;
		$this->code = $code;
		$this->flagImageURL = $flagImageURL;
		$this->menu = $menu;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getFlagImageURL() {
		return $this->flagImageURL;
	}
	
	public function getMenu() {
		return $this->menu;
	}
	
	public function getCode() {
		return $this->code;
	}
	
	public function equals(Language $lang) {
		return ($this->id == $lang->id);
	}
	
}