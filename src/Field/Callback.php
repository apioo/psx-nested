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

namespace PSX\Nested\Field;

use Closure;
use PSX\Sql\FieldInterface;

/**
 * Callback
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Callback implements FieldInterface
{
    private string $field;
    private Closure $callback;

    public function __construct(string $field, Closure $callback)
    {
        $this->field    = $field;
        $this->callback = $callback;
    }

    public function getResult(array|\ArrayAccess|null $context = null): mixed
    {
        return call_user_func($this->callback, isset($context[$this->field]) ? $context[$this->field] : null, $context);
    }
}