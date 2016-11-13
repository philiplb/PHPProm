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

class DBALSQLiteTest extends AbstractDBALTest {

    protected function setUp() {
        $connectionParams = array(
            'url' => 'sqlite:///:memory:',
        );
        $this->connectToDatabase($connectionParams);

        $sql = 'DROP TABLE IF EXISTS `phpprom`';
        $this->database->executeUpdate($sql);
        $sql = 'CREATE TABLE `phpprom` (
            `key`	TEXT NOT NULL UNIQUE,
            `value`	REAL NOT NULL,
            PRIMARY KEY(`key`)
        );';
        $this->database->executeUpdate($sql);
        $this->storage = new DBAL($this->database);
    }

}