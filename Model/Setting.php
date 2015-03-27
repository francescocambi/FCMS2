<?php
/**
 * User: Francesco
 * Date: 29/09/14
 * Time: 14.51
 */

namespace Model;

/**
 * Class Setting
 * @package Model
 * @Entity
 */
class Setting {

    /**
     * @var string
     * @Id @Column(type="string", nullable=false)
     */
    protected $settingKey;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $settingValue;

    /**
     * @param string $settingKey
     */
    public function setSettingKey($settingKey)
    {
        $this->settingKey = $settingKey;
    }

    /**
     * @return string
     */
    public function getSettingKey()
    {
        return $this->settingKey;
    }

    /**
     * @param string $settingValue
     */
    public function setSettingValue($settingValue)
    {
        $this->settingValue = $settingValue;
    }

    /**
     * @return string
     */
    public function getSettingValue()
    {
        return $this->settingValue;
    }

} 