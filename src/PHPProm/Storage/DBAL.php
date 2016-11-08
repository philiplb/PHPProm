<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPProm\Storage;

use \Doctrine\DBAL\Connection;

/**
 * Class DBAL
 * Storage implementation using Doctrine DBAL.
 * This way, MySQL and other databases are supported.
 * The used SQL is kept very simple so the queries should work
 * with most of the DBAL supported databases.
 * A MySQL example of the expected table:
 * CREATE TABLE `phpprom` (
 *     `key` varchar(255) NOT NULL,
 *     `value` double NOT NULL,
 *     PRIMARY KEY (`key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * @package PHPProm\Storage
 */
class DBAL extends AbstractStorage {

    /**
     * @var Connection
     * The DBAL connection.
     */
    protected $connection;

    /**
     * @var string
     * The table to use.
     */
    protected $table;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     * The prepared statement to check whether there is already a value for a given key.
     */
    protected $statementKeyExists;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     * The prepared statement to insert a new key value pair.
     */
    protected $statementKeyInsert;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     * The prepared statement to update the value of an existing key.
     */
    protected $statementKeyUpdate;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     * The prepared statement to increment the value of the given key.
     */
    protected $statementKeyIncr;

    /**
     * Builds the prepared statements.
     */
    protected function buildStatements() {
        $queryBuilder             = $this->connection->createQueryBuilder()
            ->select('COUNT(`key`) AS amount')->from('`'.$this->table.'`')->where('`key` = ?');
        $this->statementKeyExists = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->insert('`'.$this->table.'`')->setValue('`value`', '?')->setValue('`key`', '?');
        $this->statementKeyInsert = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->update('`'.$this->table.'`')->set('`value`', '?')->where('`key` = ?');
        $this->statementKeyUpdate = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder           = $this->connection->createQueryBuilder()
            ->update('`'.$this->table.'`')->set('`value`', '`value` + 1')->where('`key` = ?');
        $this->statementKeyIncr = $this->connection->prepare($queryBuilder->getSQL());
    }

    /**
     * DBAL constructor.
     *
     * @param Connection $connection
     * the DBAL connection
     * @param string $table
     * the table to use
     */
    public function __construct(Connection $connection, $table = 'phpprom') {
        parent::__construct();
        $this->connection = $connection;
        $this->table      = $table;
        $this->buildStatements();
    }

    /**
     * {@inheritdoc}
     */
    public function storeMeasurement($prefix, $key, $value) {
        $prefixedKey = $prefix.':'.$key;
        $this->statementKeyExists->bindValue(1, $prefixedKey);
        $this->statementKeyExists->execute();
        $exists         = $this->statementKeyExists->fetch(\PDO::FETCH_ASSOC);
        $statementStore = $exists['amount'] > 0 ? $this->statementKeyUpdate : $this->statementKeyInsert;
        $statementStore->bindValue(1, $value);
        $statementStore->bindValue(2, $prefixedKey);
        $statementStore->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function incrementMeasurement($prefix, $key) {
        $prefixedKey = $prefix.':'.$key;
        $this->statementKeyExists->bindValue(1, $prefixedKey);
        $this->statementKeyExists->execute();
        $exists             = $this->statementKeyExists->fetch(\PDO::FETCH_ASSOC);
        $statementIncrement = $exists['amount'] > 0 ? $this->statementKeyIncr : $this->statementKeyInsert;
        if ($exists['amount'] > 0) {
            $statementIncrement->bindValue(1, $prefixedKey);
        } else {
            $statementIncrement->bindValue(1, 1);
            $statementIncrement->bindValue(2, $prefixedKey);
        }
        $statementIncrement->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeasurements($prefix, array $keys, $defaultValue = 'Nan') {
        $prefixedKeys = array_map(function($key) use ($prefix) {
            return $prefix.':'.$key;
        }, $keys);
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('`key`', '`value`')
            ->from('`'.$this->table.'`')
            ->where('`key` IN (?)')
            ->setParameter(1, $prefixedKeys, Connection::PARAM_STR_ARRAY)
        ;
        $measurements = [];
        foreach ($keys as $key) {
            $measurements[$key] = $defaultValue;
        }
        $rows = $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $unprefixedKey                = substr($row['key'], strlen($prefix) + 1);
            $measurements[$unprefixedKey] = (float)$row['value'];
        }
        return $measurements;
    }
}
