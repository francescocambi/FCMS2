<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 18:23
 */

namespace Test;

$dir = explode(DIRECTORY_SEPARATOR, __DIR__);
array_pop($dir);
$dir = join(DIRECTORY_SEPARATOR, $dir);
require_once $dir."/bootstrap.php";
define("DIR", $dir);

class SilexAppTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Silex\Application
     */
    protected $app;

    protected function getConnection() {
        $this->em = initializeTestEntityManager(DIR."/");

        $pdo = $this->em->getConnection()->getWrappedConnection();

        $this->em->clear();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();

        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        return $this->createDefaultDBConnection($pdo, 'fcms_test');
    }

    protected function getDataSet() {
        return $this->createMySQLXMLDataSet(DIR.'/test/seed.xml');
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

    protected function setUp() {
        parent::setUp();
        $this->app = require(DIR.'/app.php');
        if (is_null($this->em)) {
            $this->em = initializeTestEntityManager(DIR."/");
        }
        $this->app['em'] = $this->em;

    }

} 