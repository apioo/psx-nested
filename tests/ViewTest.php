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

use PSX\Nested\Tests\View\TestView;

/**
 * ViewAbstractTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ViewTest extends TableTestCase
{
    private TestView $view;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->view = new TestView($this->connection);
    }

    public function testGetNestedResult()
    {
        $result = $this->view->getNestedResult();
        $actual = json_encode($result, JSON_PRETTY_PRINT);
        $expect = <<<JSON
{
    "totalResults": 2,
    "entries": [
        {
            "id": 2,
            "title": "Bar",
            "author": {
                "id": "urn:profile:1",
                "name": "Foo Bar",
                "uri": "https:\/\/phpsx.org"
            },
            "tags": [
                "foo",
                "bar"
            ],
            "date": "2016-03-01T00:00:00Z"
        },
        {
            "id": 1,
            "title": "Foo",
            "author": {
                "id": "urn:profile:1",
                "name": "Foo Bar",
                "uri": "https:\/\/phpsx.org"
            },
            "tags": [
                "foo",
                "bar"
            ],
            "date": "2016-03-01T00:00:00Z"
        }
    ]
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGetNestedResultKey()
    {
        $result = $this->view->getNestedResultKey();
        $actual = json_encode($result, JSON_PRETTY_PRINT);
        $expect = <<<JSON
{
    "c4ca4238": {
        "id": 1,
        "title": "Foo",
        "author": {
            "id": "urn:profile:1",
            "name": "Foo Bar",
            "uri": "https:\/\/phpsx.org"
        },
        "date": "2016-03-01T00:00:00Z"
    }
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGetNestedResultFilter()
    {
        $result = $this->view->getNestedResultFilter();
        $actual = json_encode($result, JSON_PRETTY_PRINT);
        $expect = <<<JSON
[
    {
        "id": 2,
        "title": "Bar",
        "author": {
            "id": "urn:profile:1",
            "name": "Foo Bar",
            "uri": "https:\/\/phpsx.org"
        },
        "date": "2016-03-01T00:00:00Z"
    },
    {
        "id": 1,
        "title": "Foo",
        "author": {
            "id": "urn:profile:1",
            "name": "Foo Bar",
            "uri": "https:\/\/phpsx.org"
        },
        "date": "2016-03-01T00:00:00Z"
    }
]
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }

    public function testGetNestedResultFields()
    {
        $result = $this->view->getNestedResultFields();
        $actual = json_encode($result, JSON_PRETTY_PRINT);
        $expect = <<<JSON
{
    "boolean": true,
    "callback": "bar",
    "csv": [
        "foo",
        "bar"
    ],
    "dateTime": "2017-03-05T00:00:00Z",
    "integer": 1,
    "json": {
        "foo": "bar"
    },
    "number": 12.34,
    "replace": "http:\/\/foo.com\/foo",
    "type": 1,
    "value": "bar"
}
JSON;

        $this->assertJsonStringEqualsJsonString($expect, $actual, $actual);
    }
}
