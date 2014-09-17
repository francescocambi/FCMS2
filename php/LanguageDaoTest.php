<?php

require_once("dao/LanguageDao.php");
require_once("Language.php");
require_once("LanguageBar.php");
require_once("FlagLanguageBar.php");
require_once("Connection.php");

error_reporting(E_ALL);

$languagedao = new dao\LanguageDao();
$arrayOfLanguages = $languagedao->getAll();
$languageBar = new FlagLanguageBar();
echo $languageBar->getHTML($arrayOfLanguages);

echo "\n<br><br>\n";
echo "<h1> TEST BYID </h1>\n";
$lang = array();
array_push($lang, $languagedao->getById(1));
$languageBar->getHTML($lang);

echo "\n<br><br>\n";
echo "<h1> TEST BY CODE </h1>\n";
$langg = array();
array_push($lang, $languagedao->getByCode('it'));
$languageBar->getHTML($langg);

?>