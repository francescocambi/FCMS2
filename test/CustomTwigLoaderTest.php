<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 18:13
 */

namespace Test;

require dirname(__DIR__) . '/CustomTwigLoader.php';

class CustomTwigLoaderTest extends \PHPUnit_Framework_TestCase {

    public function testFindTemplate() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/views/Login.twig', $loader->translateNameToPath("Login.twig"));
    }

    public function testFindTemplate_app() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/apps/Admin/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('App\Admin\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_app_replace() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/Application/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Application\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_app_middle_replace() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/Test/apps/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Test\App\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_plugin_replace() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/plugins/Admin/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Plugin\Admin\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_plugin_middle_replace() {
        $loader = new \CustomTwigLoader(array());
        $this->assertEquals(dirname(__DIR__).'/Test/plugins/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Test\Plugin\Module\Home\HomeView.twig'));
    }

}
 