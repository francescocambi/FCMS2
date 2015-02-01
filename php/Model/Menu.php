<?php

namespace Model;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Class Menu
 * @package Model
 * @Entity
 */
class Menu implements HierarchicalMenu {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var \Model\MenuItem
     * @OneToMany(targetEntity="MenuItem", mappedBy="menu", cascade={"persist"})
     * @OrderBy({"itemOrder" = "ASC"})
     */
    protected $items;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="Language", mappedBy="menu")
     */
    protected $languages;

    public function __construct() {
        $this->items = new ArrayCollection();
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \Model\MenuItem $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return \Model\MenuItem
     */
    public function getItems()
    {
        return $this->items;
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
     * @param \Doctrine\Common\Collections\ArrayCollection $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren() {
        $children = array();
        foreach ($this->items->toArray() as $item) {
            if (is_null($item->getParent()))
                array_push($children, $item);
        }

		return $children;
	}

    /**
     * @param MenuItem $item
     */
    public function addMenuItem(MenuItem $item) {
        $item->setMenu($this);
        $this->items->add($item);
    }
	
}

?>