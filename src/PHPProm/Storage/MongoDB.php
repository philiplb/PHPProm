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

/**
 * Class MongoDB
 * Storage implementation using MongoDB.
 * @package PHPProm\Storage
 */
class MongoDB extends AbstractStorage {

    /**
     * @var \MongoDB\Driver\Manager
     * The MongoDB Driver Manager.
     */
    protected $mongoDBManager;

    /**
     * @var string
     * The database name for the data.
     */
    protected $database;

    /**
     * @var string
     * The collection name for the data.
     */
    protected $collection;

    /**
     * MongoDB constructor.
     *
     * @param string $host
     * a mongodb:// connection URI
     * @param string $database
     * the database to use, defaults to "phppromdb"
     * @param string $collection
     * the collection to use, defaults to "measurements"
     * @param array $options
     * connection string options, defaults to []
     * @param array $driverOptions
     * any driver-specific options not included in MongoDB connection spec, defaults to []
     */
    public function __construct($host, $database = 'phppromdb', $collection = 'measurements', array $options = [], array $driverOptions = []) {
        parent::__construct();
        $this->mongoDBManager = new \MongoDB\Driver\Manager($host, $options, $driverOptions);
        $this->database = $database;
        $this->collection = $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function storeMeasurement($metric, $key, $value) {
        $bulkWrite = new \MongoDB\Driver\BulkWrite;
        $document = ['key' => $metric.':'.$key, 'value' => $value];
        $filter = ['key' => $metric.':'.$key];
        $bulkWrite->update($filter, $document, ['upsert' => true]);
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $this->mongoDBManager->executeBulkWrite($this->database.'.'.$this->collection, $bulkWrite, $writeConcern);
    }

    /**
     * {@inheritdoc}
     */
    public function incrementMeasurement($metric, $key) {
        $filter = ['key' => $metric.':'.$key];
        $options = ['limit' => 1];
        $query = new \MongoDB\Driver\Query($filter, $options);
        $results = $this->mongoDBManager->executeQuery($this->database.'.'.$this->collection, $query);
        $value = 1;
        foreach ($results as $result) {
            $value += $result->value;
            break;
        }
        $this->storeMeasurement($metric, $key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMeasurements($metric, array $keys, $defaultValue = 'Nan') {
        $prefixedKeys = array_map(function($key) use ($metric) {
            return $metric.':'.$key;
        }, $keys);

        $measurements = [];
        foreach ($keys as $key) {
            $measurements[$key] = $defaultValue;
        }
        $filter = ['key' => ['$in' => $prefixedKeys]];
        $query = new \MongoDB\Driver\Query($filter);
        $results = $this->mongoDBManager->executeQuery($this->database.'.'.$this->collection, $query);
        foreach ($results as $result) {
            $unprefixedKey                = substr($result->key, strlen($metric) + 1);
            $measurements[$unprefixedKey] = (float)$result->value;
        }
        return $measurements;
    }
}
