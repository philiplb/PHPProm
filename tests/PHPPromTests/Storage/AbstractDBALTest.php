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

abstract class AbstractDBALTest extends AbstractStorageTest {

    protected $database;

    protected $esc;

    protected function connectToDatabase($connectionParams) {
        $config = new \Doctrine\DBAL\Configuration();
        $this->database = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $this->esc = '`';
    }

    protected function getRawKey($key) {
        $sql = 'SELECT '.$this->esc.'value'.$this->esc.' FROM '.$this->esc.'phpprom'.$this->esc.' WHERE '.$this->esc.'key'.$this->esc.' = ?';
        $result = $this->database->fetchAssoc($sql, [$key]);
        return (int)$result['value'];
    }

}