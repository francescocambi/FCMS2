<?php

class Request {
	
	private $page; //Page Object
	private $language; //Language Object
	private $user; //User Object
	
	public function __construct($page, $language, $user) {
		$this->page = $page;
		$this->language = $language;
		$this->user = $user;
	}
	
	public function getPage() {
		return $this->page;
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
	public function getUser() {
		return $this->user;
	}
}
