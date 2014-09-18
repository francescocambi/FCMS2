<?php

class FunctionalityBlock extends Block {
	
	private $blockCode;
	private $internationalCaptions; //Array of InternationalCaption objects
	
	public function __construct($id, $name, $description, $code, $internationalCaptions) {
		$this->blockCode = $code;
		$this->internationalCaptions = $internationalCaptions;
		parent::__construct($id, $name, $description);
	}
	
	public function hasLanguage(Language $lang) {
		foreach ($i as $this->internationalCaptions)
			if ($i->getLanguage()->equals($lang))
				return true;
	}
	
	public function getHTML(Language $lang) {
		
		foreach ($internationalCaptions as $i) {
			if ($lang->equals($i->getLanguage()))
				$blockCode = $i->getCaptionsArrayPhp()." ".$blockCode;
		}
		
		return eval($blockCode);
		
	}
	
}