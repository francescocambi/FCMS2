<?php

namespace Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class User
 * @package Model
 * @Entity
 */
class User {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $email;

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
     * @var ArrayCollection
     * @ManyToMany(targetEntity="AccessGroup", cascade={"all"})
     */
    protected $accessGroups;
	
	public function __construct() {
        $this->accessGroups = new ArrayCollection();
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
    public function getAddress()
    {
        return $this->address;
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
    public function getCap()
    {
        return $this->cap;
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
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param ArrayCollection $accessGroups
     */
    public function setAccessGroups($accessGroups)
    {
        $this->accessGroups = $accessGroups;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAccessGroups()
    {
        return $this->accessGroups;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function getName()
    {
        return $this->name;
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
    public function getPassword()
    {
        return $this->password;
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
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $province
     */
    public function setProvince($province)
    {
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
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
    public function getSurname()
    {
        return $this->surname;
    }

	/**
	 * Check if the string passed by argument mathes user's password.
	 * @param string
	 * @returns bool
	 */
	public function checkPassword($password)
    {
		return ($this->password == $password);
	}

    /**
     * @param AccessGroup $group
     */
    public function joinAccessGroup(AccessGroup $group)
    {
        $this->accessGroups->add($group);
    }
	
}


?>