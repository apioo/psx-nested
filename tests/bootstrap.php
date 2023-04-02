<?php

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @return \Doctrine\DBAL\Connection
 */
function getConnection()
{
    static $connection;

    if ($connection) {
        return $connection;
    }

    switch (getenv('DB')) {
        case 'mysql':
            $params = [
                'dbname'   => 'psx',
                'user'     => 'root',
                'password' => 'test1234',
                'host'     => 'localhost',
                'driver'   => 'pdo_mysql',
            ];

            $params['charset'] = 'utf8';
            $params['driverOptions'] = [
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            break;

        default:
        case 'memory':
        case 'sqlite':
            $params = [
                'memory' => true,
                'driver' => 'pdo_sqlite',
            ];
            break;
    }

    $connection = \Doctrine\DBAL\DriverManager::getConnection($params);
    $schema = $connection->createSchemaManager()->introspectSchema();

    $table = $schema->createTable('psx_sql_provider_news');
    $table->addColumn('id', \Doctrine\DBAL\Types\Types::INTEGER, ['length' => 10, 'autoincrement' => true]);
    $table->addColumn('author_id', \Doctrine\DBAL\Types\Types::INTEGER, ['length' => 10]);
    $table->addColumn('title', \Doctrine\DBAL\Types\Types::STRING, ['length' => 32]);
    $table->addColumn('create_date', \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE);
    $table->setPrimaryKey(['id']);

    $table = $schema->createTable('psx_sql_provider_author');
    $table->addColumn('id', \Doctrine\DBAL\Types\Types::INTEGER, ['length' => 10, 'autoincrement' => true]);
    $table->addColumn('name', \Doctrine\DBAL\Types\Types::STRING, ['length' => 64]);
    $table->addColumn('uri', \Doctrine\DBAL\Types\Types::STRING, ['length' => 64, 'notnull' => false]);
    $table->setPrimaryKey(['id']);

    $queries = $schema->toSql($connection->getDatabasePlatform());
    foreach ($queries as $query) {
        $connection->executeQuery($query);
    }

    return $connection;
}

