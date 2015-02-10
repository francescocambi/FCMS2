<?php
/**
 * User: Francesco
 * Date: 19/09/14
 * Time: 21.16
 */

namespace test;


use Model\Language;

class FlagLanguageBarTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Model\Language[]
     */
    protected $languages = array();

    public function setUp() {
        $lang = new Language();
        $lang->setDescription("test1");
        $lang->setCode("t1");
        $lang->setFlagImageURL("image/url");
        array_push($this->languages, $lang);

        $lang = new Language();
        $lang->setDescription("test2");
        $lang->setCode("t2");
        $lang->setFlagImageURL("image/url");
        array_push($this->languages, $lang);
    }

    public function testGetHTML() {
        $languagebar = new \FlagLanguageBar($this->languages, "/");
        $html = $languagebar->getHTML();
        $expected = "<a href=\"/t1/\"><img class=\"languageflag\" src=\"image/url\" /></a><a href=\"/t2/\"><img class=\"languageflag\" src=\"image/url\" /></a>";
        $this->assertEquals($expected, $html);
    }

}
 