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
 *
 * A MySQL example of the expected table:
 * CREATE TABLE `phpprom` (
 *     `key` varchar(255) NOT NULL,
 *     `value` double NOT NULL,
 *     PRIMARY KEY (`key`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * @package PHPProm\Storage
 *
 * A SQLite example of the expected table:
 * CREATE TABLE `phpprom` (
 *     `key`	TEXT NOT NULL UNIQUE,
 *     `value`	REAL NOT NULL,
 *     PRIMARY KEY(`key`)
 * );
 *
 * A PostgreSQL example of the expected table:
 * CREATE TABLE public.phpprom (
 *     key VARCHAR(255) PRIMARY KEY NOT NULL,
 *     value DOUBLE PRECISION NOT NULL
 * );
 * CREATE UNIQUE INDEX phpprom_key_uindex ON public.phpprom (key);
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
     * @var string
     * the sign to use to escape names
     */
    protected $esc;

    /**
     * Builds the prepared statements.
     */
    protected function buildStatements() {
        $queryBuilder             = $this->connection->createQueryBuilder()
            ->select('COUNT('.$this->esc.'key'.$this->esc.') AS amount')->from($this->esc.$this->table.$this->esc)->where(''.$this->esc.'key'.$this->esc.' = ?');
        $this->statementKeyExists = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->insert($this->esc.$this->table.$this->esc)->setValue($this->esc.'value'.$this->esc, '?')->setValue(''.$this->esc.'key'.$this->esc.'', '?');
        $this->statementKeyInsert = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->update($this->esc.$this->table.$this->esc)->set($this->esc.'value'.$this->esc, '?')->where($this->esc.'key'.$this->esc.' = ?');
        $this->statementKeyUpdate = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder           = $this->connection->createQueryBuilder()
            ->update($this->esc.$this->table.$this->esc)->set($this->esc.'value'.$this->esc, $this->esc.'value'.$this->esc.' + 1')->where($this->esc.'key'.$this->esc.' = ?');
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
        $this->esc        =  $connection->getDriver()->getName() === 'pdo_pgsql' ? '"' : '`';
        $this->buildStatements();
    }

    /**
     * {@inheritdoc}
     */
    public function storeMeasurement($metric, $key, $value) {
        $prefixedKey = $metric.':'.$key;
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
    public function incrementMeasurement($metric, $key) {
        $prefixedKey = $metric.':'.$key;
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
    public function getMeasurements($metric, array $keys, $defaultValue = 'Nan') {
        $prefixedKeys = array_map(function($key) use ($metric) {
            return $metric.':'.$key;
        }, $keys);
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select($this->esc.'key'.$this->esc, $this->esc.'value'.$this->esc)
            ->from($this->esc.$this->table.$this->esc)
            ->where($this->esc.'key'.$this->esc.' IN (?)')
            ->setParameter(1, $prefixedKeys, Connection::PARAM_STR_ARRAY)
        ;
        $measurements = [];
        foreach ($keys as $key) {
            $measurements[$key] = $defaultValue;
        }
        $rows = $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $unprefixedKey                = substr($row['key'], strlen($metric) + 1);
            $measurements[$unprefixedKey] = (float)$row['value'];
        }
        return $measurements;
    }
}
