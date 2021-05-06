<?php

declare(strict_types=1);

namespace WebnetFr\DatabaseAnonymizerBundle\Tests\System;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * @author Vlad Riabchenko <vriabchenko@webnet.fr>
 */
trait SystemTestTrait
{
    /**
     * @param bool $toDatabase
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return Connection
     */
    private function getConnection(bool $toDatabase = true): Connection
    {
        $params = [
            'driver' => $GLOBALS['db_type'],
            'host' => $GLOBALS['db_host'],
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
        ];

        if ($toDatabase) {
            $params += ['dbname' => $GLOBALS['db_name']];
        }

        $config = new Configuration();

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function regenerateUsersOrders(): void
    {
        $connection = $this->getConnection(false);
        $schemaManager = $connection->getSchemaManager();

        try {
            $schemaManager->dropDatabase($GLOBALS['db_name']);
        } catch (\Exception) {
        }

        $schemaManager->createDatabase($GLOBALS['db_name']);
        $connection->close();

        $connection = $this->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $schema = $schemaManager->createSchema();

        $user = $schema->createTable('users');
        $user->addColumn('id', 'integer', ['unsigned' => true]);
        $user->addColumn('email', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('firstname', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('lastname', 'string', ['length' => 256, 'notnull' => false]);
        $user->addColumn('birthdate', 'date', ['notnull' => false]);
        $user->addColumn('phone', 'string', ['length' => 20, 'notnull' => false]);
        $user->addColumn('password', 'string', ['length' => 64, 'notnull' => false]);
        $user->setPrimaryKey(['id']);
        $user->addUniqueIndex(['id']);
        $schemaManager->createTable($user);

        $order = $schema->createTable('orders');
        $order->addColumn('id', 'integer', ['unsigned' => true]);
        $order->addColumn('address', 'string', ['length' => 256, 'notnull' => false]);
        $order->addColumn('street_address', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('zip_code', 'string', ['length' => 10, 'notnull' => false]);
        $order->addColumn('city', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('country', 'string', ['length' => 64, 'notnull' => false]);
        $order->addColumn('comment', 'text', ['notnull' => false]);
        $order->addColumn('created_at', 'datetime', ['notnull' => false]);
        $order->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $order->setPrimaryKey(['id']);
        $order->addForeignKeyConstraint($user, ['user_id'], ['id']);
        $schemaManager->createTable($order);

        $productivity = $schema->createTable('productivity');
        $productivity->addColumn('day', 'date', ['notnull' => false]);
        $productivity->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => false]);
        $productivity->addColumn('feedback', 'text', ['notnull' => false]);
        $productivity->setPrimaryKey(['day', 'user_id']);
        $productivity->addForeignKeyConstraint($user, ['user_id'], ['id']);
        $schemaManager->createTable($productivity);

        foreach (range(1, 10) as $i) {
            $connection->createQueryBuilder()
                ->insert('users')
                ->values(['id' => $i])
                ->execute();
        }

        foreach (range(1, 20) as $i) {
            $connection->createQueryBuilder()
                ->insert('orders')
                ->values(['id' => $i, 'user_id' => random_int(1, 10)])
                ->execute();
        }

        foreach (range(1, 30) as $i) {
            $connection->createQueryBuilder()
                ->insert('productivity')
                ->values([
                    'day' => $connection->quote(new \DateTime("+$i days"), 'date'),
                    'user_id' => random_int(1, 10)
                ])
                ->execute();
        }

        $connection->close();
    }
}
