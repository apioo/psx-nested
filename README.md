
# Nested

## About

This library helps to build complex nested JSON responses based on relational tables.

## Basic usage

It is also possible to build view classes which do not work on a specific table but instead
can combine multiple tables to produce a complex result.

```php
<?php

$definition = [
    'totalResults' => $this->getTable(HandlerCommentTable::class)->getCount(),
    'entries' => $this->doCollection([$this->getTable(HandlerCommentTable::class), 'findAll'], [], [
        'id' => $this->fieldInteger('id'),
        'title' => $this->fieldCallback('title', function($title){
            return ucfirst($title);
        }),
        'author' => [
            'id' => $this->fieldFormat('userId', 'urn:profile:%s'),
            'date' => $this->fieldDateTime('date'),
        ],
        'note' => $this->doEntity([$this->getTable(TableCommandTestTable::class), 'findOneById'], [new Reference('id')], [
            'comments' => true,
            'title' => 'col_text',
        ]),
        'count' => $this->doValue('SELECT COUNT(*) AS cnt FROM psx_handler_comment', [], $this->fieldInteger('cnt')),
        'tags' => $this->doColumn('SELECT date FROM psx_handler_comment', [], 'date'),
    ])
];

return $this->build($definition);

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
