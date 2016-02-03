<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 16:46
 */

namespace Plugin\Gallery\Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Gallery
 * @package Plugin\Gallery\Model
 *
 * Represent a photo gallery
 *
 * @Entity()
 */
class Gallery
{

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
    protected $name;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $dataGallery;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $thumbImage;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="GalleryItem", mappedBy="gallery", cascade={"all"})
     * @OrderBy({"photoOrder" = "ASC"})
     */
    protected $photos;

    public function __construct() {
        $this->photos = new ArrayCollection();
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
     * @return string
     */
    public function getDataGallery()
    {
        return $this->dataGallery;
    }

    /**
     * @param string $dataGallery
     */
    public function setDataGallery($dataGallery)
    {
        $this->dataGallery = $dataGallery;
    }

    /**
     * @return string
     */
    public function getThumbImage()
    {
        return $this->thumbImage;
    }

    /**
     * @param string $thumbImage
     */
    public function setThumbImage($thumbImage)
    {
        $this->thumbImage = $thumbImage;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @param GalleryItem $photo
     */
    public function addPhoto($photo) {
        $this->photos->add($photo);
        $photo->setGallery($this);
    }



}