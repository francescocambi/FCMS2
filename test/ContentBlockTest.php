<?php
/** Copyright 2014 Francesco Cambi                                          *
 * Licensed under the Apache License, Version 2.0 (the "License");          *
 * you may not use this file except in compliance with the License.         *
 * You may obtain a copy of the License at                                  *
 *     http://www.apache.org/licenses/LICENSE-2.0                           *
 * Unless required by applicable law or agreed to in writing, software      *
 * distributed under the License is distributed on an "AS IS" BASIS,        *
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. *
 * See the License for the specific language governing permissions and      *
 * limitations under the License.                                           */
/**
 * User: Francesco
 * Date: 17/09/14
 * Time: 15.04
 */

namespace test;

use CenteredBlockStyle;
use Model\ContentBlock;

class ContentBlockTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ContentBlock
     */
    protected $contentblockinst = null;
    /**
     * @var string
     */
    protected $htmlcontent = "<h1>Contenuto</h1>";

    public function setUp() {
        $this->contentblockinst = new ContentBlock();
        $this->contentblockinst->setName("blocco");
        $this->contentblockinst->setDescription("blocco");
        $this->contentblockinst->setContent($this->htmlcontent);
        $this->contentblockinst->setBgUrl("url");
        $this->contentblockinst->setBgRed(0);
        $this->contentblockinst->setBgGreen(2);
        $this->contentblockinst->setBgBlue(0);
        $this->contentblockinst->setBgOpacity(0.3);
        $this->contentblockinst->setBgRepeatx(false);
        $this->contentblockinst->setBgRepeaty(true);
        $this->contentblockinst->setBgSize("contain");
    }

    public function testConstruct() {
        $this->assertNotNull($this->contentblockinst);
    }

    public function testGetName() {
        $this->assertEquals("blocco", $this->contentblockinst->getName());
    }

    public function testGetDescription() {
        $this->assertEquals("blocco", $this->contentblockinst->getDescription());
    }

    public function testGetHTML() {
        $this->contentblockinst->setBlockStyle(new CenteredBlockStyle());
        $this->assertTrue(strpos($this->contentblockinst->getHTML(null), $this->htmlcontent) >= 0);
    }

    public function testGetBackgroundCSSNotEmpty() {
        $css = $this->contentblockinst->getBackgroundCSS();
        $this->assertEquals("background: url('url') repeat-y rgba(0, 2, 0, 0.3); background-size: contain;", $css);
    }

    public function testGetBackgroundCSSEmpty() {
        $blocknocss = new ContentBlock();
        $this->assertEquals("",$blocknocss->getBackgroundCSS());
    }

}
 