<?php

namespace Model;
use Silex\Application;

/**
 * Class FunctionalityBlock
 * @package Model
 * @Entity
 */
class FunctionalityBlock extends Block {

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $frontControllerClassName;

    /**
     * @var string
     * @column(type="string", nullable=false)
     */
    protected $frontControllerActionName;

    /**
     * @param \Silex\Application $app
     * @return string
     */
	public function getHTML($app) {

        $frontController = new $this->frontControllerClassName();
        $content = $frontController->{$this->frontControllerActionName}($app);
        return $this->getBlockStyle()->stylizeHTML($content, $this->getBackgroundCSS());

	}

    public function isEditable() {
        return false;
    }
	
}