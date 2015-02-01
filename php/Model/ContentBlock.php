<?php

namespace Model;

use \Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * @var Language
     * @ManyToMany(targetEntity="Language")
     */
    protected $languages;
	
	public function __construct() {
        $this->languages = new ArrayCollection();
    }

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

    /**
     * @param \Model\Language $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return \Model\Language
     */
    public function getLanguages()
    {
        return $this->languages;
    }

	public function getHTML($caller) {
		
		return $this->getBlockStyle()->stylizeHTML($this->content, $this->getBackgroundCSS());
		
	}
	
}

?>