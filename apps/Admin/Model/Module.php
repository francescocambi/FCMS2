<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 15:12
 */

namespace App\Admin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;

/**
 * Class Module
 * @Entity
 * @Table(name="admin_module")
 */
class Module {

    /**
     * @var int
     * @Column(type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $description;

    /**
     * @var string
     * @Column(type="string")
     */
    protected $pluginName;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $routeName;

    /**
     * @var int
     * @Column(type="integer", nullable=true)
     */
    protected $menuOrder;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $menuIconCharacter;

    /**
     * @var ArrayCollection
     * @ManyToMany(targetEntity="App\Admin\Model\Role", inversedBy="modules", fetch="EAGER")
     * @JoinTable(name="admin_module_role")
     */
    protected $allowedRoles;

    public function __construct() {
        $this->allowedRoles = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @param string $pluginName
     */
    public function setPluginName($pluginName)
    {
        $this->pluginName = $pluginName;
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * @param string $routeName
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * @return ArrayCollection
     */
    public function getAllowedRoles() {
        return $this->allowedRoles;
    }

    /**
     * @return string
     */
    public function getMenuIconCharacter()
    {
        return $this->menuIconCharacter;
    }

    /**
     * @param string $menuIconCharacter
     */
    public function setMenuIconCharacter($menuIconCharacter)
    {
        $this->menuIconCharacter = $menuIconCharacter;
    }

    /**
     * @return mixed
     */
    public function getMenuOrder()
    {
        return $this->menuOrder;
    }

    /**
     * @param mixed $menuOrder
     */
    public function setMenuOrder($menuOrder)
    {
        $this->menuOrder = $menuOrder;
    }

    /**
     * Allow $role access to this module
     *
     * @param Role $role
     */
    public function addAllowedRole(Role $role) {
        $this->allowedRoles->add($role);
        $role->getModules()->add($this);
    }

    /**
     * Remove $role access to this module
     *
     * @param Role $role
     */
    public function removeAllowedRole(Role $role) {
        $this->allowedRoles->removeElement($role);
        $role->getModules()->removeElement($this);
    }

} 