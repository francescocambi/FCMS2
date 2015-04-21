<?php
/**
 * User: Francesco
 * Date: 17/04/15
 * Time: 11:18
 */

namespace Test;


use App\Admin\Service\ModuleAuthorizationServiceProvider;
use Silex\Application;

class ModuleAuthorizationServiceProviderTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test if provider registers its functions on the container correctly
     */
    public function testRegister() {
        $app = new Application();
        $app->register(new ModuleAuthorizationServiceProvider(), array(
            ModuleAuthorizationServiceProvider::AUTHORIZER_KEY => null,
            ModuleAuthorizationServiceProvider::CONTROLLER_KEY => null
        ));

        //Checks if there is a function defined for $app[CHECK_FN_KEY]
        $this->assertInternalType('callable', $app[ModuleAuthorizationServiceProvider::CHECK_FN_KEY]);
    }

    /**
     * Test if provider boots correctly with all arguments defined
     */
    public function testBoot() {
        $authorizer = $this->getAuthorizerMock();
        $controller = $this->getControllerMock();

        $app = new Application();
        $app->register(new ModuleAuthorizationServiceProvider(), array(
            ModuleAuthorizationServiceProvider::AUTHORIZER_KEY => $authorizer,
            ModuleAuthorizationServiceProvider::CONTROLLER_KEY => $controller
        ));

        try {
            $app->boot();
        } catch (Exception $e) {
            $this->assertTrue(false, "Unexpected exception >> ".$e->getMessage());
        }

        $this->assertTrue(true);
    }

    /**
     * Test if provider throw an exception if no authorizer has been provided
     */
    public function testBootNoAuthorizerException() {
        $controller = $this->getControllerMock();

        $app = new Application();
        $app->register(new ModuleAuthorizationServiceProvider(), array(
            ModuleAuthorizationServiceProvider::CONTROLLER_KEY => $controller
        ));

        $this->setExpectedException('InvalidArgumentException');

        $app->boot();
    }

    /**
     * Test if provider throw an exception if no controller has been provided
     */
    public function testBootNoControllerException() {
        $authorizer = $this->getAuthorizerMock();

        $app = new Application();
        $app->register(new ModuleAuthorizationServiceProvider(), array(
            ModuleAuthorizationServiceProvider::AUTHORIZER_KEY => $authorizer
        ));

        $this->setExpectedException('InvalidArgumentException');

        $app->boot();
    }

    /**
     * Test if function defined by the provider
     */
    public function testCheckFunction() {
        $authorizer = $this->getAuthorizerMock();
        $controller = $this->getControllerMock();

        $app = new Application();
        $app->register(new ModuleAuthorizationServiceProvider(), array(
            ModuleAuthorizationServiceProvider::AUTHORIZER_KEY => $authorizer,
            ModuleAuthorizationServiceProvider::CONTROLLER_KEY => $controller
        ));
        $app->boot();

        $fn = $app[ModuleAuthorizationServiceProvider::CHECK_FN_KEY]('Module', 'ActionId');
        $result = $fn(null);

        $this->assertTrue($result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getAuthorizerMock()
    {
        $authorizer = $this->getMockBuilder('App\Admin\ModuleAuthorization')
            ->disableOriginalConstructor()
            ->getMock();
        $authorizer->method('checkAuthorization')
            ->will($this->returnValue(true));
        return $authorizer;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getControllerMock()
    {
        $controller = $this->getMockBuilder('object')
            ->disableOriginalConstructor()
            ->getMock();
        return $controller;
    }

} 