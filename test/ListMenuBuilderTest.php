<?php
/**
 * User: Francesco
 * Date: 18/09/14
 * Time: 17.05
 */

namespace test;


class ListMenuBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $menu = null;

    public function setUp() {

        $items = array(
            new \MenuItem(0,"item_a","url_a",0,0,array(
                new \MenuItem(1,"item_a1","url_a1",0,0,NULL),
                new \MenuItem(2,"item_a2","url_a2",0,0,array(
                    new \MenuItem(3,"item_a3","url_a3",0,0,NULL)
                ))
            )),
            new \MenuItem(4,"item_b","url_b",0,0,NULL)
        );

        $this->menu = new \Menu(0,"test_menu",null,$items);
    }

    public function testGenerateMenuHtml() {
        $expected = '<ul><li><a href="url_a">item_a</a><ul><li><a href="url_a1">item_a1</a></li><li><a href="url_a2">item_a2</a><ul><li><a href="url_a3">item_a3</a></li></ul></li></ul></li><li><a href="url_b">item_b</a></li></ul>';
        $menubuilder = new \ListMenuBuilder();
        $menubuilder->generateFor($this->menu);
        $this->assertEquals($expected, $menubuilder->getHTML());
    }

}
 