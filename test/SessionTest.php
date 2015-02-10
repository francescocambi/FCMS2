<?php
/**
 * Created by PhpStorm.
 * User: Francesco
 * Date: 10/02/15
 * Time: 11:49
 */

class SessionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Model\Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $clientip;

    protected function setUp() {
        $this->session = new \Model\Session();
        $this->clientip = "1.1.1.1";
        $this->session->setClientIpAddress($this->clientip);
        $now = new DateTime();
        $this->session->setLoginTimestamp($now);
        $this->session->setToken("TokenTokenTokenToken");
        $user = $this->getMock('Model\User');
        $this->session->setUser($user);
    }

    public function testIsValidForValidSession() {
        $this->assertTrue($this->session->isValid($this->clientip));
    }

    public function testIsValidForClosedSession() {
        $this->session->setClosingTimestamp(new DateTime());
        $this->assertFalse($this->session->isValid($this->clientip));
    }

    public function testIsValidForExpiredSession() {
        $login = $this->session->getLoginTimestamp();
        $twoHours = new DateInterval("PT90M");
        $this->session->setLoginTimestamp($login->add($twoHours));
        $this->assertFalse($this->session->isValid($this->clientip));
    }

    public function testIsValidForDifferentClientIp() {
        $differentIp = substr($this->clientip, 0, 6);
        $this->assertFalse($this->session->isValid($differentIp));
    }

}
 