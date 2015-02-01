<?php

namespace Model;

/**
 * Class PageBlock
 * @package Model
 * @Entity
 */
class PageBlock {

    /**
     * @var /Model/Page
     * @ManyToOne(targetEntity="Page", inversedBy="pageBlocks")
     * @Id
     */
    protected $page;

    /**
     * @Var /Model/Block
     * @ManyToOne(targetEntity="Block")
     * @Id
     */
    protected $block;

    /**
     * @var int
     * @Column(type="integer", nullable=false)
     * @Id
     */
    protected $blockOrder;

    /**
     * @param \Model\Block $block
     */
    public function setBlock(Block $block)
    {
        $this->block = $block;
    }

    /**
     * @return \Model\Block
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * @param int $blockOrder
     */
    public function setBlockOrder($blockOrder)
    {
        $this->blockOrder = $blockOrder;
    }

    /**
     * @return int
     */
    public function getBlockOrder()
    {
        return $this->blockOrder;
    }

    /**
     * @param \Model\Page $page
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return \Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }

}