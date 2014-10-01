<?php
/**
 * User: Francesco
 * Date: 18/09/14
 * Time: 17.05
 */

namespace test;

use Model\Menu;
use Model\MenuItem;

class ListMenuBuilderTest extends \PHPUnit_Framework_TestCase {

    protected $menu = null;

    public function setUp() {

        $this->menu = new Menu();
        $this->menu->setName("test_null");

        $item1 = new MenuItem();
        $item1->setLabel("item_a");
        $item1->setUrl("url_a");
        $item1->setItemOrder(0);
        $item1->setHidden(false);
        $this->menu->addMenuItem($item1);

        $itema1 = new MenuItem();
        $itema1->setLabel("item_a1");
        $itema1->setUrl("url_a1");
        $itema1->setItemOrder(0);
        $itema1->setHidden(false);
        $item1->addChild($itema1);

        $itema2 = new MenuItem();
        $itema2->setLabel("item_a2");
        $itema2->setUrl("url_a2");
        $itema2->setItemOrder(1);
        $itema2->setHidden(false);
        $item1->addChild($itema2);

        $itema3 = new MenuItem();
        $itema3->setLabel("item_a3");
        $itema3->setUrl("url_a3");
        $itema3->setItemOrder(2);
        $itema3->setHidden(false);
        $itema2->addChild($itema3);

        $item2 = new MenuItem();
        $item2->setLabel("item_b");
        $item2->setUrl("url_b");
        $item2->setItemOrder(1);
        $item2->setHidden(false);
        $this->menu->addMenuItem($item2);

    }

    public function testGenerateMenuHtml() {
        $expected = '<ul><li><a href="url_a">item_a</a><ul><li><a href="url_a1">item_a1</a></li><li><a href="url_a2">item_a2</a><ul><li><a href="url_a3">item_a3</a></li></ul></li></ul></li><li><a href="url_b">item_b</a></li></ul>';
        $menubuilder = new \ListMenuBuilder();
        $menubuilder->generateFor($this->menu);
        $this->assertEquals($expected, $menubuilder->getHTML());
    }

}
 