<?php

class ContentBlock extends Block {
	
	private $content;
	private $languages; //Array of Language obects
	
	public function __construct($id, $name, $description, $content, $languages, $bgurl, $bgred, $bggreen, $bgblue, $bgopacity, $bgrepeatx, $bgrepeaty, $bgsize) {
		$this->content = $content;
		$this->languages = $languages;
		parent::__construct($id, $name, $description, $bgurl, $bgred, $bggreen, $bgblue, $bgopacity, $bgrepeatx, $bgrepeaty, $bgsize);
	}

	public function hasLanguage(Language $lang) {
		foreach ($this->languages as $i)
			if ($lang->equals($i))
				return true;
	}
	
	public function getHTML(Language $lang) {
		
		return $this->getBlockStyle()->stylizeHTML($this->content, $this->getBackgroundCSS());
		
	}
	
}

?>