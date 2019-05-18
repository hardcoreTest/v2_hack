<?php

use Doctrine\DBAL\Schema\Table;
use \Doctrine\DBAL\Types\Type;

/**
 * Created by PhpStorm.
 * User: user
 * Date: 21.02.2017
 * Time: 16:21
 */
class migration201711281529
{
    public function up(Doctrine\DBAL\Schema\AbstractSchemaManager $schema, Doctrine\DBAL\Connection $connection)
    {
        $name = new \Doctrine\DBAL\Schema\Column(
            'name', Type::getType(Type::STRING), ['length' => '255', 'notnull' => true]
        );
        $user = new \Doctrine\DBAL\Schema\TableDiff(
            'user',
            [$name]
        );
        $schema->alterTable($user);
    }
}