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

use PHPProm\Storage\MongoDB;

class MongoDBTest extends AbstractStorageTest {

    protected $mongoDBManager;

    protected function setUp() {
        $this->storage = new MongoDB('mongodb://localhost:27017');
        $this->mongoDBManager = new \MongoDB\Driver\Manager('mongodb://localhost:27017');

        $bulkDelete = new \MongoDB\Driver\BulkWrite;
        $bulkDelete->delete(['key' => 'metric:key']);
        $bulkDelete->delete(['key' => 'metric:incrementKey']);
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongoDBManager->executeBulkWrite('phppromdb.measurements', $bulkDelete, $writeConcern);
    }

    protected function getRawKey($key) {
        $filter = ['key' => $key];
        $options = ['limit' => 1];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $results = $this->mongoDBManager->executeQuery('phppromdb.measurements', $query);
        foreach ($results as $result) {
            return $result->value;
        }
        return null;
    }

}