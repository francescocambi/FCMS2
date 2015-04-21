<?php
/**
 * User: Francesco
 * Date: 20/09/14
 * Time: 12.25
 */

namespace test;


use Core\CenteredBlockStyle;
use Model\ContentBlock;
use Model\AccessGroup;
use Model\Page;
use Model\PageBlock;
use Model\User;

class PageTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Model\Page
     */
    private $pageinst;

    /**
     * @var \Model\User
     */
    private $user;

    public function setUp() {

        $this->pageinst = new Page();
        $this->pageinst->setName("test");
        $this->pageinst->setTitle("test");
        $this->pageinst->setPublished(true);
        $this->pageinst->setPublic(true);

        $block = new ContentBlock();
        $block->setName("testblock");
        $block->setContent("<h1>This is a block</h1>");
        $block->setBlockStyle(new CenteredBlockStyle());
        $pageBlock = new PageBlock();
        $pageBlock->setBlock($block);
        $pageBlock->setBlockOrder(1);
        $this->pageinst->addPageBlock($pageBlock);

    }

    public function testIsPublished() {
        $this->assertTrue($this->pageinst->isPublished());
    }

    public function testIsPublic() {
        $this->assertTrue($this->pageinst->isPublic());
    }

    public function testGetBlocks() {
        $this->assertTrue(count($this->pageinst->getPageBlocks()->toArray()) > 0);
    }

}
 