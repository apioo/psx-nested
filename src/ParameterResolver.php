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

namespace PSX\Nested;

use PSX\Nested\Exception\BuilderException;

/**
 * ParameterResolver
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class ParameterResolver
{
    /**
     * Resolves parameters against a context
     *
     * @param array $parameters
     * @param array|\ArrayAccess|null $context
     * @return array
     */
    public static function resolve(array $parameters, array|\ArrayAccess|null $context = null)
    {
        $params = [];
        foreach ($parameters as $key => $value) {
            if ($value instanceof Reference) {
                $val = $value->getValue();
                if (is_array($context) && array_key_exists($val, $context)) {
                    $params[$key] = $context[$val];
                } elseif ($context instanceof \ArrayAccess && $context->offsetExists($val)) {
                    $params[$key] = $context->offsetGet($val);
                } else {
                    throw new BuilderException('Reference invalid context key "' . $val . '"');
                }
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
