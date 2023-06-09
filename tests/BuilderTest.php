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

use PSX\Nested\Builder;
use PSX\Nested\Exception\BuilderException;
use PSX\Nested\Field;
use PSX\Nested\Map;
use PSX\Record\Record;

/**
 * BuilderTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class BuilderTest extends TableTestCase
{
    public function testBuild()
    {
        $news = [[
            'id' => 1,
            'authorId' => 1,
            'title' => 'foo',
            'createDate' => '2016-03-01 00:00:00',
        ],[
            'id' => 2,
            'authorId' => 1,
            'title' => 'bar',
            'createDate' => '2016-03-01 00:00:00',
        ]];

        $author = [
            'id' => 1,
            'name' => 'Foo Bar',
            'uri' => 'http://phpsx.org',
        ];

        $definition = [
            'totalEntries' => 2,
            'entries' => new Map\Collection($news, [
                'id' => 'id',
                'title' => new Field\Callback('title', function($title){
                    return ucfirst($title);
                }),
                'isNew' => new Field\Value(true),
                'author' => new Map\Entity($author, [
                    'displayName' => 'name',
                    'uri' => 'uri',
                ]),
                'date' => new Field\DateTime('createDate'),
                'links' => [
                    'self' => new Field\Format('id', 'http://foobar.com/news/%s'),
                ]
            ])
        ];

        $expect = <<<JSON
{
    "totalEntries": 2,
    "entries": [
        {
            "id": 1,
            "title": "Foo",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00Z",
            "links": {
                "self": "http:\/\/foobar.com\/news\/1"
            }
        },
        {
            "id": 2,
            "title": "Bar",
            "isNew": true,
            "author": {
                "displayName": "Foo Bar",
                "uri": "http:\/\/phpsx.org"
            },
            "date": "2016-03-01T00:00:00Z",
            "links": {
                "self": "http:\/\/foobar.com\/news\/2"
            }
        }
    ]
}
JSON;

        $builder = new Builder($this->connection);
        $result  = json_encode($builder->build($definition), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $result, $result);
    }

    public function testBuildUnknownFieldInContext()
    {
        $this->expectException(BuilderException::class);

        $data = [
            'foo' => 'bar',
        ];

        $definition = [
            'fields' => new Map\Entity($data, [
                'test' => 'test'
            ]),
        ];

        $builder = new Builder($this->connection);
        $builder->build($definition);
    }

    public function testBuildUnknownFieldWithoutContext()
    {
        $definition = [
            'foo' => 'bar',
        ];

        $builder = new Builder($this->connection);
        $result  = $builder->build($definition);

        $this->assertEquals(Record::fromArray($definition), $result);
    }

    public function testBuildFieldWithNullValue()
    {
        $news = [[
            'id' => 1,
            'title' => 'foo',
        ],[
            'id' => 2,
            'title' => null,
        ]];

        $definition = [
            'entries' => new Map\Collection($news, [
                'id' => 'id',
                'title' => 'title'
            ])
        ];

        $expect = <<<JSON
{
    "entries": [
        {
            "id": 1,
            "title": "foo"
        },
        {
            "id": 2
        }
    ]
}
JSON;

        $builder = new Builder($this->connection);
        $result  = json_encode($builder->build($definition), JSON_PRETTY_PRINT);

        $this->assertJsonStringEqualsJsonString($expect, $result, $result);
    }

}