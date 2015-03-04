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
     * @var ArrayCollection
     * @ManyToMany(targetEntity="App\Admin\Model\Role", inversedBy="modules")
     */
    protected $allowedRoles;

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

} 