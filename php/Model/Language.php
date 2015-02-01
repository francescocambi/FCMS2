<?php

namespace Model;

/**
 * Class Language
 * @package Model
 * @Entity
 */
class Language {

    /**
     * @var int
     * @Id
     * @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $description;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $flagImageURL;

    /**
     * @var string
     * @Column(type="string", length=2, nullable=false, unique=true)
     */
    protected $code;

    /**
     * @var \Model\Menu
     * @ManyToOne(targetEntity="Menu", inversedBy="languages")
     */
    protected $menu;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $flagImageURL
     */
    public function setFlagImageURL($flagImageURL)
    {
        $this->flagImageURL = $flagImageURL;
    }

    /**
     * @return string
     */
    public function getFlagImageURL()
    {
        return $this->flagImageURL;
    }

    /**
     * @param \Model\Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return \Model\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    } //Menu object

    /**
     * @param Language $lang
     * @return bool true if this and $lang are the same Language
     */
    public function equals(Language $lang)
    {
		return ($this->id == $lang->id);
	}
	
}