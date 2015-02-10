<?php

namespace App\Site\LanguageBar;

use \Silex\Application;
    
interface ILanguageBar {

    public function __construct(Application $app, array $languages);
	
	public function getHTML();
	
}
    
?>