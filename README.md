
# Nested

## About

This library helps to build complex nested JSON responses based on relational tables.

## Basic usage

It is also possible to build view classes which do not work on a specific table but instead
can combine multiple tables to produce a complex result.

```php
<?php

$connection = null; // a doctrine DBAL connection
$builder = new \PSX\Nested\Builder($connection);

$definition = [
    'totalResults' => $this->getTable(HandlerCommentTable::class)->getCount(),
    'entries' => $builder->doCollection([$this->getTable(HandlerCommentTable::class), 'findAll'], [], [
        'id' => $builder->fieldInteger('id'),
        'title' => $builder->fieldCallback('title', function($title){
            return ucfirst($title);
        }),
        'author' => [
            'id' => $builder->fieldFormat('userId', 'urn:profile:%s'),
            'date' => $builder->fieldDateTime('date'),
        ],
        'note' => $builder->doEntity([$this->getTable(TableCommandTestTable::class), 'findOneById'], [new Reference('id')], [
            'comments' => true,
            'title' => 'col_text',
        ]),
        'count' => $builder->doValue('SELECT COUNT(*) AS cnt FROM psx_handler_comment', [], $this->fieldInteger('cnt')),
        'tags' => $builder->doColumn('SELECT date FROM psx_handler_comment', [], 'date'),
    ])
];

return $builder->build($definition);

```

The `getNestedResult` method would produce the following json response

```json
{
  "totalResults": 4,
  "entries": [
    {
      "id": 4,
      "title": "Blub",
      "author": {
        "id": "urn:profile:3",
        "date": "2013-04-29T16:56:32Z"
      },
      "count": 4,
      "tags": [
        "2013-04-29 16:56:32",
        "2013-04-29 16:56:32",
        "2013-04-29 16:56:32",
        "2013-04-29 16:56:32"
      ]
    },
    ...
  ]
}
```
