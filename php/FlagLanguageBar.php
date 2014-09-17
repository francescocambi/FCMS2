<?php

class FlagLanguageBar implements LanguageBar {
	
	public function getHTML(array $languages) {
		$html = "";
		//print_r($_GET);
		foreach ($languages as $language) {
			$html .= "<a href=\"".$this->generateURLForLanguage($language)."\"><img class=\"languageflag\" src=\"".$language->getFlagImageURL()."\" /></a>";
		}
		return $html;
	}
	
	private function generateURLForLanguage(Language $language) {
		$exploded = explode("/", $_SERVER['REQUEST_URI']);
		
		if ($exploded[count($exploded)-1] == "")
			array_pop($exploded);
		
		if (isset($_GET['url']) && $_GET['url'] != "")
			array_pop($exploded);
		if (isset($_GET['lang']))
			array_pop($exploded);
		
		array_push($exploded, $language->getCode());
		if (isset($_GET['url']) && $_GET['url'] != "")
			array_push($exploded, $_GET['url']);
		
		$string = "";
		foreach ($exploded as $part)
			$string .= $part."/";
		
		//echo substr($string, strlen($string)-1, 1);
		
		if (substr($string, strlen($string)-1, 1) == "/")
			$string = substr($string, 0, strlen($string)-1);
		
		//echo "----".$string;
		
		return $string;
	}
	
}

?>
