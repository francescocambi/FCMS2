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

class ContentBlockTest extends \PHPUnit_Framework_TestCase {

    protected $contentblockinst = null;

    public function setUp() {
        $this->contentblockinst = new \ContentBlock(0, "blocco", "blocco", "<h1>Contenuto</h1>", null, "url",1,2,1,0.3,false,true,"contain");
    }

    public function testConstruct() {
        $this->assertNotNull($this->contentblockinst);
    }

    public function testGetHTML() {

    }

    public function testGetBackgroundCSS() {
        $css = $this->contentblockinst->getBackgroundCSS();
        $this->assertEquals("background: url('url') rgba(1, 2, 1, 0.3) repeat-y; background-size: contain;", $css);
    }

}
 