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

namespace PSX\Nested\DBAL;

use Doctrine\DBAL\Connection;
use PSX\Nested\ProviderCollectionInterface;
use PSX\Nested\ProviderColumnInterface;
use PSX\Nested\ProviderEntityInterface;
use PSX\Nested\ProviderValueInterface;

/**
 * Factory
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Factory implements FactoryInterface
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function newCollection(string $sql, array $parameters, mixed $definition, mixed $key = null, \Closure $filter = null): ProviderCollectionInterface
    {
        return new Collection($this->connection, $sql, $parameters, $definition, $key);
    }

    public function newEntity(string $sql, array $parameters, mixed $definition): ProviderEntityInterface
    {
        return new Entity($this->connection, $sql, $parameters, $definition);
    }

    public function newColumn(string $sql, array $parameters, mixed $definition): ProviderColumnInterface
    {
        return new Column($this->connection, $sql, $parameters, $definition);
    }

    public function newValue(string $sql, array $parameters, mixed $definition): ProviderValueInterface
    {
        return new Value($this->connection, $sql, $parameters, $definition);
    }
}
