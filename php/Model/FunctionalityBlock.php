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


	public function getHTML($app) {

//        print $app;
//        $frontController = new $this->frontControllerClassName();
//        return $frontController->{$this->frontControllerActionName}();

	}
	
}