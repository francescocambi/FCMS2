<?php
/**
 * User: Francesco
 * Date: 16/02/15
 * Time: 15:01
 */

namespace App\Admin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class Role
 * @Entity
 * @Table(name="admin_role")
 */
class Role implements RoleInterface {

    /**
     * @var int
     * @Column(type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var ArrayCollection
     * @ManyToMany(targetEntity="App\Admin\Model\Module", mappedBy="allowedRoles")
     * @JoinTable(name="admin_module_role")
     */
    protected $modules;

    public function __construct() {
        $this->modules = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @param ArrayCollection $modules
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * Grant access to $module for this role
     *
     * @param Module $module
     */
    public function addModule($module) {
        $this->modules->add($module);
        $module->getAllowedRoles()->add($this);
    }

    /**
     * Returns the role.
     *
     * This method returns a string representation whenever possible.
     *
     * When the role cannot be represented with sufficient precision by a
     * string, it should return null.
     *
     * @return string|null A string representation of the role, or null
     */
    public function getRole()
    {
        return $this->name;
    }


} 