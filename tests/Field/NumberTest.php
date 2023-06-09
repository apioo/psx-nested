<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Nested\Tests\Field;

use PHPUnit\Framework\TestCase;
use PSX\Nested\Field\Number;

/**
 * NumberTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class NumberTest extends TestCase
{
    public function testTransform()
    {
        $field = new Number('foo');

        $this->assertEquals(1.2, $field->getResult(['foo' => '1.2']));
        $this->assertEquals(-1.2, $field->getResult(['foo' => '-1.2']));
        $this->assertEquals(0, $field->getResult(['foo' => 'a']));
        $this->assertEquals(1.2, $field->getResult(['foo' => 1.2]));
        $this->assertEquals(-1.2, $field->getResult(['foo' => -1.2]));
    }
}
