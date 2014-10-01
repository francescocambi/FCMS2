<?php

namespace Model;

/**
 * Class FunctionalityBlock
 * @package Model
 * @Entity
 */
class FunctionalityBlock extends Block {

    /**
     * @var string
     * @Column(type="text", nullable=false)
     */
    protected $blockCode;

    /**
     * @var
     */
    private $internationalCaptions; //Array of InternationalCaption objects

	public function getHTML($caller) {

        //Check if $caller->getLanguage() is supported by this block.
        //Otherwise sets a default language

        //Print captions array in code for evaluation
		foreach ($this->internationalCaptions as $i) {
			if ($caller->getLanguage()->equals($i->getLanguage()))
				$blockCode = $i->getCaptionsArrayPhp()." ".$this->blockCode;
		}
		
		return eval($blockCode);
		
	}
	
}