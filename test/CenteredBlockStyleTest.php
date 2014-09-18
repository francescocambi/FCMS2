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
 * Date: 18/09/14
 * Time: 15.50
 */

namespace test;


class CenteredBlockStyleTest extends \PHPUnit_Framework_TestCase {

    public function testStylizeHTML() {
        $html = "<h1>This is a test</h1>";
        $localcss = "some css: code;";
        $blockstyle = new \CenteredBlockStyle();

        $stylized = $blockstyle->stylizeHTML($html, $localcss);
        $this->assertTrue(strpos($stylized, $html) && strpos($stylized, $localcss));

    }

}
 