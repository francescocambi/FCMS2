<?php
/**
 * User: Francesco
 * Date: 16/04/15
 * Time: 16:28
 */

namespace Test;

use App\Admin\ModuleAuthorization;
use Symfony\Component\HttpFoundation\Response;

require_once(dirname(__DIR__)."/vendor/autoload.php");

class ModuleAuthorizationTest extends SilexAppTestCase {

    public function getDataSet() {
        return $this->createMySQLXMLDataSet(__DIR__.'/authorizationTest_seed.xml');
    }

    /**
     * Test checkAuthorization when user is not fully authenticated
     */
    public function testCheckAuthorizationRedirectToLogin() {

        $security = $this->getSecurityContextMock(false, false);
        $url_generator = $this->getUrlGeneratorMock();

        $app = $this->getApplicationMock(array(
            'security' => $security,
            'url_generator' => $url_generator
        ));

        //Test method behaviour
        $authorizer = new ModuleAuthorization($app);
        $response = $authorizer->checkAuthorization("Second", null);
        $this->assertEquals('/login', $response->getContent());
    }

    /**
     * Test checkAuthorization when user cannot access requested resource
     */
    public function testCheckAuthorizationForbidden() {

        //Set up security mock object

        $security = $this->getSecurityContextMock(true, false);

        $url_generator = $this->getUrlGeneratorMock();

        $app = $this->getApplicationMock(array(
            'em' => $this->em,
            'security' => $security,
            'url_generator' => $url_generator
        ));

        //Prepare controller mock object
        $controller = $this->getMockBuilder('object')
            ->setMethods(array('render'))
            ->getMock();
        $controller->method('render')
            ->will($this->returnValue('Forbidden Page'));

        //Test method behaviour
        $authorizer = new ModuleAuthorization($app);
        $response = $authorizer->checkAuthorization("Second", $controller);
        $this->assertEquals('Forbidden Page', $response);

    }

    /**
     * Test checkAuthorization when user is able to access requested resource
     */
    public function testCheckAuthorizationGranted() {
        $security = $this->getSecurityContextMock(true, true);

        $app = $this->getApplicationMock(array(
            'em' => $this->em,
            'security' => $security
        ));

        //Prepare controller mock object
        $controller = $this->getMockBuilder('object')
            ->setMethods(array('render'))
            ->getMock();
        $controller->method('render')
            ->will($this->returnValue('Forbidden Page'));

        $authorizer = new ModuleAuthorization($app);
        $response = $authorizer->checkAuthorization("Second", $controller);
        $this->assertNull($response);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getSecurityContextMock($authenticatedFully, $accessGranted) {
        //Set up security mock object
        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $security->method('isGranted')
            ->will($this->returnCallback(function ($a) use ($authenticatedFully, $accessGranted) {
                $map = array(
                    "IS_AUTHENTICATED_FULLY" => $authenticatedFully,
                    "ROLE_ADMIN" => $accessGranted,
                    "ROLE_USER" => $accessGranted
                );
                return $map[$a];
            }));
        return $security;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getUrlGeneratorMock()
    {
        //Set up url generator mock object

        $url_generator = $this->getMockBuilder('Symfony\Component\Routing\Generator\UrlGenerator')
            ->disableOriginalConstructor()
            ->getMock();

        $url_generator->method('generate')
            ->will($this->returnCallback(function ($a) {
                $map = array(
                    'login' => '/login'
                );
                return $map[$a];
            }));

        return $url_generator;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getApplicationMock($containerMap)
    {
        //Set up application mock object
        $app = $this->getMockBuilder('Silex\Application')
            ->disableOriginalConstructor()
            ->getMock();

        $app->method('redirect')
            ->will($this->returnCallback(function ($arg0) {
                $response = new Response($arg0);
                return $response;
            }));

        //Prepare app container
        $app->method('offsetGet')
            ->will($this->returnCallback(function ($a) use ($containerMap) {
                    return $containerMap[$a];
                }
            ));
        return $app;
    }

}