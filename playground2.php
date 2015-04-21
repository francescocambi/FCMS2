<?php
/**
 * User: Francesco
 * Date: 20/04/15
 * Time: 17:28
 */


$app = require_once('core/app.php');

$blocks = $app['em']->getRepository('Model\ContentBlock')->findAll();

//$linkDestinations = array_map('extract_links', $blocks);

function extract_links($block) {
    $input = $block->getContent();
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
    if(preg_match_all("/$regexp/siU", $input, $matches)) {
        return $matches[2];
        // $matches[2] = array of link addresses
        // $matches[3] = array of link text - including HTML code
    }

}

$content = $blocks[100]->getContent();

print "------- BEFORE -------\n";
print $content;
print "\n----------------------\n";

$dom = \Sunra\PhpSimple\HtmlDomParser::str_get_html($content);

$links = $dom->find("a");

foreach ($links as $link) {
//    print $link->getAttribute('href')."\n";
    if (str_replace("/", "", $link->getAttribute('href')) == 'lavorazioni-a-disegno') {
        $link->setAttribute('href', 'prodotti-custom');
    }
}

print "------- AFTER -------\n";
print $dom;
print "\n---------------------\n";