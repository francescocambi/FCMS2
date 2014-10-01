<?php
/**
 * User: Francesco
 * Date: 20/09/14
 * Time: 12.23
 */

namespace test;

use \Model\Language;

class LanguageTest extends \PHPUnit_Framework_TestCase {

    public function testEquals() {
        $first = new Language();
        $first->setId(1);

        $second = new Language();
        $second->setId(1);

        $third = new Language();
        $third->setId(3);

        $this->assertTrue($first->equals($second) && !$third->equals($second));
    }

}
 