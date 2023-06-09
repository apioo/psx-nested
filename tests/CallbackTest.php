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

namespace PSX\Nested\Tests;

use PSX\Nested\Callback;
use PSX\Nested\Field;
use PSX\Nested\Reference;

/**
 * CallbackTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class CallbackTest extends ProviderTestCase
{
    protected $authorId;

    protected function getDefinition()
    {
        $this->authorId = 0;

        return [
            'totalEntries' => new Callback\Value([$this, 'dataTotal'], [], new Field\Integer('cnt')),
            'entries' => new Callback\Collection([$this, 'dataNews'], [], [
                'id' => new Field\Integer('id'),
                'title' => 'title',
                'tags' => new Callback\Column([$this, 'dataNews'], [], 'title'),
                'author' => new Callback\Entity([$this, 'dataAuthor'], [new Reference('authorId'), 'bar'], [
                    'displayName' => 'name',
                    'uri' => 'uri',
                ]),
            ])
        ];
    }

    public function dataNews()
    {
        return [[
            'id' => 1,
            'authorId' => 1,
            'title' => 'foo',
            'createDate' => '2016-03-01 00:00:00',
        ], [
            'id' => 2,
            'authorId' => 2,
            'title' => 'bar',
            'createDate' => '2016-03-01 00:00:00',
        ]];
    }

    public function dataAuthor($authorId, $foo)
    {
        $this->authorId++;
        $this->assertEquals($this->authorId, $authorId);
        $this->assertEquals('bar', $foo);

        return [
            'name' => 'Foo Bar',
            'uri' => 'https://phpsx.org'
        ];
    }

    public function dataTotal()
    {
        return ['cnt' => 2];
    }
}
