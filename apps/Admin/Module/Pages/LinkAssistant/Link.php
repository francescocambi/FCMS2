<?php
/**
 * User: Francesco
 * Date: 22/04/15
 * Time: 16:19
 */

namespace App\Admin\Module\Pages\LinkAssistant;


use Model\ContentBlock;
use Model\Url;

class Link {

    /** @var Url */
    private $url;
    /** @var ContentBlock */
    private $block;
    /** @var \simple_html_dom_node */
    private $domElement;

    /**
     * @return ContentBlock
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param ContentBlock $block
     */
    public function setBlock($block)
    {
        $this->block = $block;
    }

    /**
     * @return \simple_html_dom_node
     */
    public function getDomElement()
    {
        return $this->domElement;
    }

    /**
     * @param \simple_html_dom_node $domElement
     */
    public function setDomElement($domElement)
    {
        $this->domElement = $domElement;
    }

    /**
     * @return Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Url $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

} 