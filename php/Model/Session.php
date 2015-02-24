<?php
namespace Model;

/**
 * Class Session
 * @package Model
 * @Entity
 */
class Session {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var User
     * @ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @var DateTime
     * @Column(type="datetime", nullable=false)
     */
    protected $loginTimestamp;

    /**
     * @var DateTime
     * @Column(type="datetime", nullable=true)
     */
    protected $closingTimestamp;

    /**
     * @var string
     * @Column(type="string", length=20, nullable=false)
     */
    protected $clientIpAddress;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $token;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $clientIpAddress
     */
    public function setClientIpAddress($clientIpAddress)
    {
        $this->clientIpAddress = $clientIpAddress;
    }

    /**
     * @return string
     */
    public function getClientIpAddress()
    {
        return $this->clientIpAddress;
    }

    /**
     * @param \DateTime $closingTimestamp
     */
    public function setClosingTimestamp($closingTimestamp)
    {
        $this->closingTimestamp = $closingTimestamp;
    }

    /**
     * @return \DateTime
     */
    public function getClosingTimestamp()
    {
        return $this->closingTimestamp;
    }

    /**
     * @param \DateTime $loginTimestamp
     */
    public function setLoginTimestamp($loginTimestamp)
    {
        $this->loginTimestamp = $loginTimestamp;
    }

    /**
     * @return \DateTime
     */
    public function getLoginTimestamp()
    {
        return $this->loginTimestamp;
    }

    /**
     * @param \Model\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Check session validity
     * @param string $clientIp Client ip address
     * @return bool
     */
    public function isValid($clientIp) {
        //If logout has happened, session has been closed
        if ( !is_null( $this->getClosingTimestamp() ) ) {
            return false;
        }

        //If client ip address is different, user must login again on new client
        if ( $this->getClientIpAddress() != $clientIp ) {
            return false;
        }

        $now = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
        //If login was done more than 1h ago, session is expired
        if ( $this->getLoginTimestamp()->diff($now, true)->h >= 1 ) {
            return false;
        }

        //Otherwise session is still valid
        return true;
    }



}