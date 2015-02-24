<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 17:36
 */

$dir = explode(DIRECTORY_SEPARATOR, __DIR__);
array_pop($dir);
$dir = join(DIRECTORY_SEPARATOR, $dir);
require_once $dir."/bootstrap.php";
define("DIR", $dir);

class AdminMenuTest extends Test\SilexAppTestCase {

    public function testRenderMenu() {
//        $menuController = new \App\Admin\Menu\Menu($this->app);
//        print $menuController->renderMenu();
    }

}
 