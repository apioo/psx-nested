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

use Doctrine\DBAL\Connection;
use PSX\Record\Record;
use PSX\Record\RecordableInterface;
use PSX\Nested\Exception\BuilderException;

/**
 * The build method resolves the definition through calling every provider and
 * field objects. The result is an array in the format of the definition
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://phpsx.org
 */
class Builder
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns an array based on the resolved definition
     *
     * @throws BuilderException
     */
    public function build(mixed $definition, array|\ArrayAccess|null $context = null): mixed
    {
        if ($definition instanceof ProviderInterface) {
            return $this->getProviderValue($definition, $context);
        } elseif ($definition instanceof FieldInterface) {
            return $definition->getResult($context);
        } elseif (is_array($definition)) {
            $result = new Record();
            foreach ($definition as $key => $value) {
                $result->put($key, $this->build($value, $context));
            }

            return $result;
        } elseif (is_string($definition)) {
            if ($context !== null) {
                if (is_array($context)) {
                    if (array_key_exists($definition, $context)) {
                        return $context[$definition];
                    } else {
                        throw new BuilderException('Referenced unknown key "' . $definition . '" in context');
                    }
                } elseif ($context instanceof \ArrayAccess) {
                    if ($context->offsetExists($definition)) {
                        return $context->offsetGet($definition);
                    } else {
                        throw new BuilderException('Referenced unknown key "' . $definition . '" in context');
                    }
                } else {
                    throw new BuilderException('Context must be either an array or instance of ArrayAccess');
                }
            } else {
                return $definition;
            }
        } else {
            return $definition;
        }
    }

    public function doCollection(callable|string|array $source, array $arguments, array $definition, string|\Closure|null $key = null, ?\Closure $filter = null): ProviderCollectionInterface
    {
        if (is_callable($source)) {
            return new Callback\Collection($source, $arguments, $definition, $key, $filter);
        } elseif (is_string($source)) {
            return new DBAL\Collection($this->connection, $source, $arguments, $definition, $key, $filter);
        } else {
            return new Map\Collection($source, $definition, $key, $filter);
        }
    }

    public function doEntity(callable|string|array $source, array $arguments, array $definition): ProviderEntityInterface
    {
        if (is_callable($source)) {
            return new Callback\Entity($source, $arguments, $definition);
        } elseif (is_string($source)) {
            return new DBAL\Entity($this->connection, $source, $arguments, $definition);
        } else {
            return new Map\Entity($source, $definition);
        }
    }

    public function doColumn(callable|string|array $source, array $arguments, mixed $definition): ProviderColumnInterface
    {
        if (is_callable($source)) {
            return new Callback\Column($source, $arguments, $definition);
        } elseif (is_string($source)) {
            return new DBAL\Column($this->connection, $source, $arguments, $definition);
        } else {
            return new Map\Column($source, $definition);
        }
    }

    public function doValue(callable|string|array $source, array $arguments, mixed $definition): ProviderValueInterface
    {
        if (is_callable($source)) {
            return new Callback\Value($source, $arguments, $definition);
        } elseif (is_string($source)) {
            return new DBAL\Value($this->connection, $source, $arguments, $definition);
        } else {
            return new Map\Value($source, $definition);
        }
    }

    public function fieldBoolean(string $value): Field\Boolean
    {
        return new Field\Boolean($value);
    }

    public function fieldCallback(string $key, \Closure $callback): Field\Callback
    {
        return new Field\Callback($key, $callback);
    }

    public function fieldCsv(string $key, string $delimiter = ','): Field\Csv
    {
        return new Field\Csv($key, $delimiter);
    }

    public function fieldDateTime(string $value): Field\DateTime
    {
        return new Field\DateTime($value);
    }

    public function fieldInteger(string $value): Field\Integer
    {
        return new Field\Integer($value);
    }

    public function fieldJson(string $value): Field\Json
    {
        return new Field\Json($value);
    }

    public function fieldNumber(string $value): Field\Number
    {
        return new Field\Number($value);
    }

    public function fieldFormat(string $value, string $format): Field\Format
    {
        return new Field\Format($value, $format);
    }

    public function fieldValue(string $value): Field\Value
    {
        return new Field\Value($value);
    }

    /**
     * @throws BuilderException
     */
    protected function getProviderValue(ProviderInterface $provider, array|\ArrayAccess|null $context = null): mixed
    {
        $data = $provider->getResult($context);
        $definition = $provider->getDefinition();
        $result = null;

        if (empty($data)) {
            return null;
        }

        if ($provider instanceof ProviderCollectionInterface) {
            $result = [];
            $key    = $provider->getKey();
            $filter = $provider->getFilter();

            if ($key === null) {
                foreach ($data as $row) {
                    if ($row instanceof RecordableInterface) {
                        $row = $row->toRecord();
                    }

                    $result[] = $this->build($definition, $row);
                }
            } elseif (is_string($key)) {
                foreach ($data as $row) {
                    if ($row instanceof RecordableInterface) {
                        $row = $row->toRecord();
                    }

                    $result[$row[$key]] = $this->build($definition, $row);
                }
            } else {
                foreach ($data as $row) {
                    if ($row instanceof RecordableInterface) {
                        $row = $row->toRecord();
                    }

                    $return = call_user_func_array($key, [$row]);
                    $result[$return] = $this->build($definition, $row);
                }
            }

            if ($filter !== null) {
                $result = call_user_func_array($filter, [$result]);
            }
        } elseif ($provider instanceof ProviderEntityInterface) {
            if ($data instanceof RecordableInterface) {
                $data = $data->toRecord();
            }

            $result = $this->build($definition, $data);
        } elseif ($provider instanceof ProviderColumnInterface) {
            $result = [];
            foreach ($data as $row) {
                if ($row instanceof RecordableInterface) {
                    $row = $row->toRecord();
                }

                $result[] = $this->build($definition, $row);
            }
        } elseif ($provider instanceof ProviderValueInterface) {
            if ($data instanceof RecordableInterface) {
                $data = $data->toRecord();
            }

            $result = $this->build($definition, $data);
        }

        return $result;
    }
}

