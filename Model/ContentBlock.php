<?php

namespace Model;

use \Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
/**
 * Class ContentBlock
 * @package Model
 * @Entity
 */
class ContentBlock extends Block {

    /**
     * @var string
     * @Column(type="text", nullable=false)
     */
    protected $content;
	
	public function __construct() {}

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

	public function getHTML($app) {
		
		return $this->getBlockStyle()->stylizeHTML($this->content, $this->getBackgroundCSS());
		
	}

    public function isEditable() {
        return true;
    }
	
}

?>