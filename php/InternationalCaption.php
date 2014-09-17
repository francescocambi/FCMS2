<?php

class InternationalCaption {
	
	private $captionsArray = array(); //Associative array $['KEY']='VALUE';
	private $language; //Language Object
	
	public function __construct($captionsArray, $language) {
		$this->captionsArray = $captionsArray;
		$this->language = $language;
	}
	
	public function getCaptionsArray() {
		return $this->captionsArray;
	}
	
	public function updateCaptionsArray($key, $value) {
		$captionsArray[$key] = $value;
	}
	
	public function getCaptionsArrayPhp() {
		$returnString = '$CAPTIONS = array( ';
		foreach ($this->captionsArray as $key => $value) {
			$returnString .= '"'.$key.'" => "'.$value.'",';
		}
		$returnString = substr($returnString, 0, strlen($returnString)-2);
		$returnString .= ");";
	}
	
	public function getLanguage() {
		return $this->language;
	}
	
}