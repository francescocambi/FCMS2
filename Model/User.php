<?php

namespace Model;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package Model
 * @Entity
 */
class User implements AdvancedUserInterface, EquatableInterface {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $email;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $username;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $surname;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $phone;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $address;

    /**
     * @var string
     * @Column(type="string", length=5)
     */
    protected $cap;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $city;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $province;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $country;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $roles;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $salt;

    /**
     * @var \DateTime
     * @Column(type="datetime", nullable=true)
     */
    protected $accountExpiration;

    /**
     * @var bool
     * @Column(type="boolean", nullable=false)
     */
    protected $enabled;

    /**
     * @var \DateTime
     * @Column(type="datetime", nullable=true)
     */
    protected $credentialsExpiration;

	public function __construct() {
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getAccountExpiration()
    {
        return $this->accountExpiration;
    }

    /**
     * @param \DateTime $accountExpiration
     */
    public function setAccountExpiration($accountExpiration)
    {
        $this->accountExpiration = $accountExpiration;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCap()
    {
        return $this->cap;
    }

    /**
     * @param string $cap
     */
    public function setCap($cap)
    {
        $this->cap = $cap;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return \DateTime
     */
    public function getCredentialsExpiration()
    {
        return $this->credentialsExpiration;
    }

    /**
     * @param \DateTime $credentialsExpiration
     */
    public function setCredentialsExpiration($credentialsExpiration)
    {
        $this->credentialsExpiration = $credentialsExpiration;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return \Symfony\Component\Security\Core\Role\Role[]
     */
    public function getRoles()
    {
        return explode(',', $this->roles);
    }

    /**
     * @param \Symfony\Component\Security\Core\Role\Role[] $roles
     */
    public function setRoles($roles)
    {
        $this->roles = join(',', $roles);
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * {{@inheritdoc}}
     */
    public function eraseCredentials() {

    }

    /**
     * @return bool|void
     */
    public function isAccountNonExpired() {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
        return $this->accountExpiration > $now;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked() {
        return $this->enabled;
    }

    /**
     * @return bool|void
     */
    public function isCredentialsNonExpired() {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Rome'));
        return $this->credentialsExpiration > $now;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user) {
        if ($this->username === $user->getUsername()) {
            return true;
        } else {
            return false;
        }
    }
}


?>