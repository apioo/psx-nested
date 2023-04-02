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

namespace PSX\Nested\Tests\View;

use Doctrine\DBAL\Connection;
use PSX\Nested\Builder;
use PSX\Nested\Reference;

/**
 * TestView
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class TestView
{
    private Connection $connection;
    private Builder $builder;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->builder = new Builder($connection);
    }

    public function getNestedResult()
    {
        $definition = [
            'totalResults' => $this->builder->doValue('SELECT COUNT(*) AS cnt FROM psx_sql_provider_news', [], $this->builder->fieldInteger('cnt')),
            'entries' => $this->builder->doCollection('SELECT * FROM psx_sql_provider_news ORDER BY id DESC', [], [
                'id' => $this->builder->fieldInteger('id'),
                'title' => $this->builder->fieldCallback('title', function($title){
                    return ucfirst($title);
                }),
                'author' => $this->builder->doEntity('SELECT * FROM psx_sql_provider_author WHERE id = ?', [new Reference('author_id')], [
                    'id' => $this->builder->fieldFormat('id', 'urn:profile:%s'),
                    'name' => 'name',
                    'uri' => 'uri',
                ]),
                'tags' => $this->builder->doColumn('SELECT title FROM psx_sql_provider_news', [], 'title'),
                'date' => $this->builder->fieldDateTime('create_date'),
            ])
        ];

        return $this->builder->build($definition);
    }

    public function getNestedResultKey()
    {
        $definition = $this->builder->doCollection('SELECT * FROM psx_sql_provider_news ORDER BY id DESC', [], [
            'id' => $this->builder->fieldInteger('id'),
            'title' => $this->builder->fieldCallback('title', function($title){
                return ucfirst($title);
            }),
            'author' => $this->builder->doEntity('SELECT * FROM psx_sql_provider_author WHERE id = ?', [new Reference('author_id')], [
                'id' => $this->builder->fieldFormat('id', 'urn:profile:%s'),
                'name' => 'name',
                'uri' => 'uri',
            ]),
            'date' => $this->builder->fieldDateTime('create_date'),
        ], function($row){
            return substr(md5($row['author_id']), 0, 8);
        });

        return $this->builder->build($definition);
    }

    public function getNestedResultFilter()
    {
        $definition = $this->builder->doCollection('SELECT * FROM psx_sql_provider_news ORDER BY id DESC', [], [
            'id' => $this->builder->fieldInteger('id'),
            'title' => $this->builder->fieldCallback('title', function($title){
                return ucfirst($title);
            }),
            'author' => $this->builder->doEntity('SELECT * FROM psx_sql_provider_author WHERE id = ?', [new Reference('author_id')], [
                'id' => $this->builder->fieldFormat('id', 'urn:profile:%s'),
                'name' => 'name',
                'uri' => 'uri',
            ]),
            'date' => $this->builder->fieldDateTime('create_date'),
        ], null, function(array $result){
            return array_values(array_filter($result, function($row){
                return $row['author']['id'] == 'urn:profile:1';
            }));
        });

        return $this->builder->build($definition);
    }

    public function getNestedResultFields()
    {
        $data = [
            'boolean' => '1',
            'callback' => 'foo',
            'csv' => 'foo,bar',
            'dateTime' => '2017-03-05 00:00:00',
            'integer' => '1',
            'json' => '{"foo": "bar"}',
            'number' => '12.34',
            'replace' => 'foo',
            'type' => '1',
            'value' => 'foo',
        ];

        $definition = $this->builder->doEntity($data, [], [
            'boolean' => $this->builder->fieldBoolean('boolean'),
            'callback' => $this->builder->fieldCallback('callback', function(){
                return 'bar';
            }),
            'csv' => $this->builder->fieldCsv('csv'),
            'dateTime' => $this->builder->fieldDateTime('dateTime'),
            'integer' => $this->builder->fieldInteger('integer'),
            'json' => $this->builder->fieldJson('json'),
            'number' => $this->builder->fieldNumber('number'),
            'replace' => $this->builder->fieldFormat('replace', 'http://foo.com/%s'),
            'type' => $this->builder->fieldInteger('type'),
            'value' => $this->builder->fieldValue('bar'),
        ]);

        return $this->builder->build($definition);
    }
}
