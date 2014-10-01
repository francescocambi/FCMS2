<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class MenuItem
 * @package Model
 * @Entity
 */
class MenuItem implements HierarchicalMenu {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $label;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $url;

    /**
     * @var int
     * @Column(type="integer", nullable=false)
     */
    protected $itemOrder;

    /**
     * @var boolean
     * @Column(type="boolean", nullable=false)
     */
    protected $hidden;

    /**
     * @var \Model\Menu
     * @ManyToOne(targetEntity="Menu", inversedBy="items", cascade={"all"})
     * @JoinColumn(name="menu_id", referencedColumnName="id")
     */
    protected $menu;

    /**
     * @var ArrayCollection
     * @OneToMany(targetEntity="MenuItem", mappedBy="parent", cascade={"all"})
     */
    protected $children;

    /**
     * @var MenuItem
     * @ManyToOne(targetEntity="MenuItem", inversedBy="children", cascade={"all"})
     */
    protected $parent;

    public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }

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
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param int $order
     */
    public function setItemOrder($order)
    {
        $this->itemOrder = $order;
    }

    /**
     * @return int
     */
    public function getItemOrder()
    {
        return $this->itemOrder;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @param \Model\Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return \Model\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren() {
		return $this->children->toArray();
	}

    /**
     * @param MenuItem $item
     */
    public function addChild(MenuItem $item) {
        $item->setParent($this);
        $this->children->add($item);
    }
	
}
?>