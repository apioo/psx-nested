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

namespace PSX\Nested\Callback;

use PSX\Nested\ProviderCollectionInterface;

/**
 * Collection
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Collection extends CallbackAbstract implements ProviderCollectionInterface
{
    private string|\Closure|null $key;
    private ?\Closure $filter;

    public function __construct(callable $callback, array $parameters, mixed $definition, string|\Closure|null $key = null, ?\Closure $filter = null)
    {
        parent::__construct($callback, $parameters, $definition);

        $this->key    = $key;
        $this->filter = $filter;
    }

    public function getKey(): string|\Closure|null
    {
        return $this->key;
    }

    public function getFilter(): ?\Closure
    {
        return $this->filter;
    }
}
