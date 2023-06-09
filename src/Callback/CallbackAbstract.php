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

use PSX\Nested\ParameterResolver;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
abstract class CallbackAbstract
{
    protected \Closure $callback;
    protected array $parameters;
    protected mixed $definition;

    public function __construct(callable $callback, array $parameters, mixed $definition)
    {
        $this->callback   = \Closure::fromCallable($callback);
        $this->parameters = $parameters;
        $this->definition = $definition;
    }

    public function getResult(array|\ArrayAccess|null $context = null): mixed
    {
        try {
            return call_user_func_array($this->callback, ParameterResolver::resolve($this->parameters, $context));
        } catch (\TypeError $e) {
            return null;
        }
    }

    public function getDefinition(): mixed
    {
        return $this->definition;
    }
}
