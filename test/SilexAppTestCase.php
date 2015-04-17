<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 18:23
 */

namespace Test;

use Core\EntityManagerFactory;

require_once(dirname(__DIR__).'/vendor/autoload.php');

class SilexAppTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Silex\Application
     */
    protected $app;

    protected function getConnection() {

        if (is_null($this->app))
            $this->app = $this->getApplication();

        $this->em = EntityManagerFactory::initializeTestEntityManager($this->app);

        $pdo = $this->em->getConnection()->getWrappedConnection();

        $this->em->clear();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        return $this->createDefaultDBConnection($pdo, 'fcms_test');
    }

    protected function getDataSet() {
        return $this->createMySQLXMLDataSet(dirname(__DIR__).'/test/seed.xml');
    }

//    public function getMockApplicationObject() {
//        $em = $this->em;
//        $app = $this->getMockBuilder('Silex\Application')
//            ->disableOriginalConstructor()->getMock();
//        $app->expects($this->any())
//            ->method('offsetGet')
//            ->will($this->returnCallback(
//                function ($key) use ($em) {
//                    if ($key == "em") return $em;
//                    return null;
//                }
//            ));
//        return $app;
//    }

    private function getApplication() {
        return require(dirname(__DIR__)."/core/app.php");
    }

    protected function setUp() {
        parent::setUp();
        if (is_null($this->app))
            $this->app = $this->getApplication();
    }

} 