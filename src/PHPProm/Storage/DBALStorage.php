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

class DBALStorage extends AbstractStorage {

    protected $connection;

    protected $table;

    protected $statementKeyExists;

    protected $statementKeyInsert;

    protected $statementKeyUpdate;

    protected function buildStatements() {
        $queryBuilder             = $this->connection->createQueryBuilder()
            ->select('COUNT(`key`) AS amount')
            ->from('`'.$this->table.'`')
            ->where('`key` = ?')
        ;
        $this->statementKeyExists = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->insert('`'.$this->table.'`')
            ->setValue('`value`', '?')
            ->setValue('`key`', '?')
        ;
        $this->statementKeyInsert = $this->connection->prepare($queryBuilder->getSQL());

        $queryBuilder             = $this->connection->createQueryBuilder()
            ->update('`'.$this->table.'`')
            ->set('`value`', '?')
            ->where('`key` = ?')
        ;
        $this->statementKeyUpdate = $this->connection->prepare($queryBuilder->getSQL());
    }

    public function __construct(Connection $connection, $table = 'phpprom') {
        $this->connection = $connection;
        $this->table      = $table;
        $this->buildStatements();
    }

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

    public function getMeasurements($prefix, array $keys) {
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
            $measurements[$key] = 'Nan';
        }
        $rows = $queryBuilder->execute()->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $unprefixedKey                = substr($row['key'], strlen($prefix) + 1);
            $measurements[$unprefixedKey] = (float)$row['value'];
        }
        return $measurements;
    }
}
