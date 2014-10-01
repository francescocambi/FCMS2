<?php

namespace Model;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Page
 * @package Model
 * @Entity
 */
class Page {

    /**
     * @var int
     * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @Column(type="string", nullable=false, unique=true)
     */
    protected $name;

    /**
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @var bool
     * @Column(type="boolean", nullable=false)
     */
    protected $published;

    /**
     * @var bool
     * @Column(type="boolean", nullable=false)
     */
    protected $public;

    /**
     * @var \Model\Language
     * @ManyToOne(targetEntity="Language")
     */
    protected $language;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="PageBlock", mappedBy="page", cascade={"all"})
     * @OrderBy({"blockOrder" = "ASC"})
     */
    protected $pageBlocks;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="AccessGroup", cascade={"all"})
     */
    protected $accessGroups;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @OneToMany(targetEntity="Url", mappedBy="page", cascade={"all"})
     */
    protected $pageUrls;
	
	public function __construct() {
        $this->pageBlocks = new ArrayCollection();
        $this->accessGroups = new ArrayCollection();
        $this->pageUrls = new ArrayCollection();
    }
	
	public function getId() {
		return $this->id;
	}

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $accessGroups
     */
    public function setAccessGroups($accessGroups)
    {
        $this->accessGroups = $accessGroups;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAccessGroups()
    {
        return $this->accessGroups;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $pageBlocks
     */
    public function setPageBlocks($pageBlocks)
    {
        $this->pageBlocks = $pageBlocks;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPageBlocks()
    {
        return $this->pageBlocks;
    }

    /**
     * @return \Model\PageBlock[]
     */
    public function getPageBlocksArray()
    {
        return $this->pageBlocks->toArray();
    }

    /**
     * @param \Model\Language $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return \Model\Language
     */
    public function getLanguage()
    {
        return $this->language;
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
     * @param boolean $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return boolean
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $pageUrls
     */
    public function setPageUrls($pageUrls)
    {
        $this->pageUrls = $pageUrls;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPageUrls()
    {
        return $this->pageUrls;
    }

    /**
     * @return bool
     */
    public function isPublished() {
		return $this->published;
	}

    /**
     * @return bool
     */
    public function isPublic() {
		return $this->public;
	}

    /**
     * @param AccessGroup $newGroup
     */
    public function addAccessGroup(AccessGroup $newGroup) {
        $this->accessGroups->add($newGroup);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function canBeViewedBy(User $user) {
        /**
         * @var AccessGroup
         */
        $group = null;
        $userGroups = $user->getAccessGroups()->toArray();
		foreach ($this->accessGroups->toArray() as $group)
			foreach ($userGroups as $userGroup)
				if ($group->equals($userGroup))
					return true;
				
		return false;
		
	}

    /**
     * @param PageBlock $pageBlock
     */
    public function addPageBlock(PageBlock $pageBlock)
    {
        $pageBlock->setPage($this);
        $this->pageBlocks->add($pageBlock);
    }

    /**
     * @param $block Block
     * @param $blockOrder int
     */
    public function addBlock($block, $blockOrder) {
        $pageblock = new PageBlock();
        $pageblock->setBlock($block);
        $pageblock->setBlockOrder($blockOrder);
        $this->pageBlocks->add($pageblock);
        $pageblock->setPage($this);
    }

    /**
     * @param $url Url
     */
    public function addUrl($url) {
        $this->pageUrls->add($url);
        $url->setPage($this);
    }

}

?>