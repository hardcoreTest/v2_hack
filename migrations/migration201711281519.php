<?php

use Doctrine\DBAL\Schema\Table;
use \Doctrine\DBAL\Types\Type;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.02.2017
 * Time: 16:21
 */
class migration201711281519
{
    public function up(Doctrine\DBAL\Schema\AbstractSchemaManager $schema, Doctrine\DBAL\Connection $connection)
    {
        if (!$schema->tablesExist('user')) {
            $users = new Table('user');
            $users->addColumn('id', Type::GUID, ['notnull' => false]);
            $users->setPrimaryKey(['id']);
            $users->addColumn('type_user', Type::GUID, ['length' => 64]);
            $users->addIndex(['type_user'], 'type_user');
            $users->addColumn('type_waste', Type::STRING, ['length' => 1024]);
            $users->addIndex(['type_waste'], 'type_waste');
            $users->addColumn('coordinate', Type::STRING, ['length' => 1024]);
            $users->addColumn('contact', Type::STRING, ['length' => 255, 'notnull' => false]);
            $users->addColumn('email', Type::STRING, ['length' => 255]);
            $users->addColumn('date_create', Type::DATETIME );
            $users->addColumn('date_update', Type::DATETIME );
            $schema->createTable($users);

        }
    }
}