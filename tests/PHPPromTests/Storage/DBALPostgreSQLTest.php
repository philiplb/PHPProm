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

class DBALPostgreSQLTest extends AbstractDBALTest {

    protected function setUp() {
        $connectionParams = array(
            'url' => 'postgresql://localhost:5432/postgres',
        );
        $this->connectToDatabase($connectionParams);
        $this->esc = '"';


        $sql = 'DROP TABLE IF EXISTS phpprom';
        $this->database->executeUpdate($sql);
        $sql = 'CREATE TABLE public.phpprom (
              key VARCHAR(255) PRIMARY KEY NOT NULL,
              value DOUBLE PRECISION NOT NULL
            )';
        $this->database->executeUpdate($sql);
        $sql = 'CREATE UNIQUE INDEX phpprom_key_uindex ON public.phpprom (key);';
        $this->database->executeUpdate($sql);

        $this->storage = new DBAL($this->database);
    }

}