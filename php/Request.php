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

    /**
     * @return Model\Page
     */
    public function getPage() {
		return $this->page;
	}

    /**
     * @return Model\Language
     */
    public function getLanguage() {
		return $this->language;
	}

    /**
     * @return Model\User
     */
    public function getUser() {
		return $this->user;
	}
}
