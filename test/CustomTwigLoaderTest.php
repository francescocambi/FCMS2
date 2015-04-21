<?php
/**
 * User: Francesco
 * Date: 20/02/15
 * Time: 18:13
 */

namespace Test;

use Core\CustomTwigLoader;

class CustomTwigLoaderTest extends \PHPUnit_Framework_TestCase {

    private $rootPath;

    public function setUp() {
        $this->rootPath = dirname(__DIR__);
    }

    public function testFindTemplate() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/views/Login.twig', $loader->translateNameToPath("Login.twig"));
    }

    public function testFindTemplate_app() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/apps/Admin/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('App\Admin\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_app_replace() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/Application/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Application\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_app_middle_replace() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/Test/apps/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Test\App\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_plugin_replace() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/plugins/Admin/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Plugin\Admin\Module\Home\HomeView.twig'));
    }

    public function testFindTemplate_plugin_middle_replace() {
        $loader = new CustomTwigLoader($this->rootPath);
        $this->assertEquals($this->rootPath.'/Test/plugins/Module/Home/views/HomeView.twig',
            $loader->translateNameToPath('Test\Plugin\Module\Home\HomeView.twig'));
    }

}
 