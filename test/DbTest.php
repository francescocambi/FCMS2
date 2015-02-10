<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 10/02/15
 * Time: 11:04
 */

$dir = explode(DIRECTORY_SEPARATOR, __DIR__);
array_pop($dir);
$dir = join(DIRECTORY_SEPARATOR, $dir);
require_once $dir."/bootstrap.php";
define("DIR", $dir);

class DbTest extends PHPUnit_Extensions_Database_TestCase {

    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    public function getConnection() {
        $em = initializeTestEntityManager(DIR."/");
        $this->em = $em;

        $pdo = $em->getConnection()->getWrappedConnection();

        $em->clear();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $classes = $em->getMetadataFactory()->getAllMetadata();

        $tool->dropSchema($classes);
        $tool->createSchema($classes);

        return $this->createDefaultDBConnection($pdo, 'fcms_test');
    }

    public function getDataSet() {
        return $this->createMySQLXMLDataSet(DIR.'/test/seed.xml');
    }

    public function testSomething() {
        $pages = $this->em->getRepository('Model\Page')->findAll();
        $this->assertTrue(count($pages) > 0);
    }

} 