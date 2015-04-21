<?php
/**
 * User: Francesco
 * Date: 06/03/15
 * Time: 10:34
 */

namespace  ;


use App\Admin\Module\Menu\EditorController;

class AdminModuleMenuEditorControllerTest extends \PHPUnit_Framework_TestCase {

    public function testProcessMenu() {

        $data = array(
            'menu_name' => 'altro',
            'menu_description' => 'altro',
            'label' => array(
                0 => 'primo',
                1 => 'secondo',
                2 => 'sottosecondo',
                3 => 'sottosottosecondo',
                4 => 'terzo',
                5 => 'sottoterzo',
                6 => ''
            ),
            'url' => array(
                0 => '',
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => ''
            ),
            'level' => array(
                0 => '0',
                1 => '0',
                2 => '1',
                3 => '2',
                4 => '0',
                5 => '1',
                6 => ''
            ),
            'order' => array(
                0 => '',
                1 => '',
                2 => '',
                3 => '',
                4 => '',
                5 => ''
            )
        );

        $controller = new EditorController();
        $items = $controller->processMenu($data, 0, array());

        $this->assertTrue(count($items) == 3);
        $this->assertEquals("primo", $items[0]->getLabel());
        $this->assertEquals("secondo", $items[1]->getLabel());
        $this->assertEquals("terzo", $items[2]->getLabel());

    }

}
 