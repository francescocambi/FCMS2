<?php
    
interface LanguageBar {

    public function __construct(array $languages, $baseurl);
	
	public function getHTML();
	
}
    
?>