<?php
/**
 * User: Francesco
 * Date: 06/02/15
 * Time: 17:46
 */

namespace Plugin\ContactMe\Model;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;


/**
 * Class Message
 * @package Plugin\ContactMe\Model
 *
 * Represent a message sent by contact me form
 *
 * @Entity()
 */
class Message {


    /**
     * @var integer
     * @Id()
     * @Column(type="integer")
     * @GeneratedValue()
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $referrer;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $email;

    /**
     * @var string
     * @Column(type="text", nullable=false)
     */
    protected $text;

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
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

} 