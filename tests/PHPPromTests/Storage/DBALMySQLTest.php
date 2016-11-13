<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPPromTests\Storage;

use PHPProm\Storage\DBAL;

class DBALMySQLTest extends AbstractStorageTest {

    protected $database;

    protected function setUp() {
        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'url' => 'mysql://root:@localhost/phppromtest',
        );
        $this->database = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $this->storage = new DBAL($this->database);

        $sql = 'DROP TABLE IF EXISTS `phpprom`';
        $this->database->executeUpdate($sql);
        $sql = 'CREATE TABLE `phpprom` (
              `key` varchar(255) NOT NULL,
              `value` double NOT NULL,
              PRIMARY KEY (`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
        $this->database->executeUpdate($sql);
    }

    protected function getRawKey($key) {
        $sql = 'SELECT `value` FROM phpprom WHERE `key` = ?';
        $result = $this->database->fetchAssoc($sql, [$key]);
        return (int)$result['value'];
    }

}