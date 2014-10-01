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
     * @OneToMany(targetEntity="MenuItem", mappedBy="menu", cascade={"all"})
     */
    protected $items;

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
     * @return MenuItem[]
     */
    public function getChildren() {
		return $this->items->toArray();
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