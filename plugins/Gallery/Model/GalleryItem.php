<?php
/**
 * User: Francesco
 * Date: 07/10/15
 * Time: 16:50
 */

namespace Plugin\Gallery\Model;

/**
 * Class GalleryItem
 * @package Plugin\Gallery\Model
 *
 * Represent a Photo in a Gallery
 *
 * @Entity()
 */
class GalleryItem
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
    protected $imageUrl;

    /**
     * @var Gallery
     * @ManyToOne(targetEntity="Gallery")
     */
    protected $gallery;

    /**
     * @var integer
     * @Column(type="integer", nullable=false)
     */
    protected $photoOrder;

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
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * @param Gallery $gallery
     */
    public function setGallery($gallery)
    {
        $this->gallery = $gallery;
        $gallery->getPhotos()->add($this);
    }

    /**
     * @return int
     */
    public function getPhotoOrder()
    {
        return $this->photoOrder;
    }

    /**
     * @param int $photoOrder
     */
    public function setPhotoOrder($photoOrder)
    {
        $this->photoOrder = $photoOrder;
    }

}