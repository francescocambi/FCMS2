<?php
/**
 * User: Francesco
 * Date: 22/04/15
 * Time: 16:14
 */

namespace App\Admin\Module\Pages\LinkAssistant;


use Model\ContentBlock;
use Model\Url;
use Sunra\PhpSimple\HtmlDomParser;

class LinkAssistant {

    /**
     * @param \Model\Url $url
     * @return Link[]
     */
    public function searchLinks($blocks, $url) {
        $links = array();

        foreach ($blocks as $block) {
            $dom = HtmlDomParser::str_get_html($block->getContent());
            $domLinks = $dom->find('a');
            foreach ($domLinks as $domLink) {
                if ($this->checkSimilarity($domLink->getAttribute('href'), "/".$url->getUrl())) {
                    $link = new Link();
                    $link->setBlock($block);
                    $link->setUrl($url);
                    $link->setDomElement($domLink);
                    array_push($links, $link);
                }
            }
        }

        return $links;
    }

    private function checkSimilarity($a, $b)
    {
        if ($a[0] == "/" && $b[0] != "/") {
            $a[0] = '';
        }
        if ($a[0] != "/" && $b[0] != "/") {
            $b[0] = '';
        }

        if ($a[strlen($a)-1] == "/")
            $a[strlen($a)-1] = "";

        if ($b[strlen($b)-1] == "/")
            $b[strlen($b)-1] = "";

        return $a == $b;
    }

    /**
     * @param ContentBlock[] $blocks
     * @param Url $oldUrl
     * @param Url $newUrl
     * @return ContentBlock[] blocks with refactored links
     */
    public function refactorLinks($blocks, $oldUrl, $newUrl) {
        foreach ($blocks as $block) {
            /** @var \simple_html_dom $dom */
            $dom = HtmlDomParser::str_get_html($block->getContent());
            $domLinks = $dom->find('a');
            foreach ($domLinks as $domLink) {
                if ($this->checkSimilarity($domLink->getAttribute('href'), "/".$oldUrl->getUrl())) {
                    $domLink->setAttribute('href', $this->newUrlString($domLink->getAttribute('href'), $newUrl->getUrl()));
                }
            }
            $block->setContent($dom);
        }

        return $blocks;
    }

    /**
     * @param string $currentUrl
     * @param string $newUrl
     * @return string
     */
    private function newUrlString($currentUrl, $newUrl) {

        if ($currentUrl[0] == "/" && $newUrl[0] != "/")
            $newUrl = "/".$newUrl;
        else if ($newUrl[0] == "/")
            $newUrl[0] = "";


        if ($currentUrl[strlen($currentUrl)-1] == "/" && $newUrl[strlen($newUrl)] != "/")
            $newUrl = $newUrl."/";
        else if ($newUrl[strlen($newUrl)-1] == "/")
            $newUrl[strlen($newUrl)] = "";

        return $newUrl;
    }

} 