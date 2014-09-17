<?php
/**
 * User: Francesco
 * Date: 17/09/14
 * Time: 11.48
 */

namespace test;


abstract class BlockTest extends \PHPUnit_Framework_TestCase {

    public $blockInstance = null;

    /**
     * @beforeClass
     */
    public abstract function setUpObject() {
    }

    public function testGetBackgroundCSS() {

    }

}
 