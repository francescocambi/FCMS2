<?php

use \Model\Language;

class FlagLanguageBar implements LanguageBar {

    private $languages;
    private $baseurl;

    public function __construct(array $languages, $baseurl) {
        $this->languages = $languages;
        $this->baseurl = $baseurl;
    }
	
	public function getHTML() {
		$html = "";
		//print_r($_GET);
		foreach ($this->languages as $language) {
			// $html .= "<a href=\"".$this->generateURLForLanguage($language)."\"><img class=\"languageflag\" src=\"".$language->getFlagImageURL()."\" /></a>";
            $html .= "<a href=\"".$this->generateURLForLanguageGoToHome($language)."\"><img class=\"languageflag\" src=\"".$language->getFlagImageURL()."\" /></a>";

        }
		return $html;
	}

    /**
     * Append language code in URL, so user remains in the same page
     * but the overall site language is changed with the clicked one
     * @param Language $language
     * @return string
     */
	private function generateURLForLanguageSamePage(Language $language) {
		$exploded = explode("/", $this->baseurl);
		
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

        if (!isset($_GET['url']) || (isset($_GET['url']) && $_GET['url'] == "")) {
            $string .= "/";
        }
		
		return $string;
	}

    /**
     * Generated URL points to the home page for the clicked language
     * WARN: I'm not appending language code, otherwise if language is italian
     * and user clicks italian another time url will be it/it/ violating
     * url syntax requirements
     * @param Language $language
     */
    private function generateURLForLanguageGoToHome(Language $language) {
        $exploded = explode("/", $this->baseurl);

        if ($exploded[count($exploded)-1] == "")
            array_pop($exploded);

        if (isset($_GET['url']) && $_GET['url'] != "")
            array_pop($exploded);
        if (isset($_GET['lang']))
            array_pop($exploded);

        array_push($exploded, $language->getCode());

        $string = "";
        foreach ($exploded as $part)
            $string .= $part."/";

        //echo substr($string, strlen($string)-1, 1);

        if (substr($string, strlen($string)-1, 1) == "/")
            $string = substr($string, 0, strlen($string)-1);

        //echo "----".$string;

        $string .= "/";

        return $string;
    }

}

?>
