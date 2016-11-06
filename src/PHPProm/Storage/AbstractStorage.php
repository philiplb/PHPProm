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
 * Class AbstractStorage
 * The parent class of all storage implementations.
 * @package PHPProm\Storage
 */
abstract class AbstractStorage {

    /**
     * @var array
     * Holds the available metrics.
     */
    protected $availableMetrics;

    /**
     * AbstractStorage constructor.
     */
    public function __construct() {
        $this->availableMetrics = [];
    }

    /**
     * Adds a metric to the available ones.
     *
     * @param string s$storagePrefix
     * the prefix for the stored metric key
     * @param string $metric
     * the metric itself
     * @param string $label
     * the name of the one Prometheus label to categorize the values
     * @param string $help
     * a helping text for the metric
     * @param string $type
     * the Prometheus type of the metric
     * @param string $defaultValue
     * the default value which the metric gets if there is no value stored
     */
    public function addAvailableMetric($storagePrefix, $metric, $label, $help, $type, $defaultValue) {
        $this->availableMetrics[] = [
            'storagePrefix' => $storagePrefix,
            'metric' => $metric,
            'label' => $label,
            'help' => $help,
            'type' => $type,
            'defaultValue' => $defaultValue
        ];
    }

    /**
     * Gets all available metrics in an array.
     *
     * @return array
     * the available metrics
     */
    public function getAvailableMetrics() {
        return $this->availableMetrics;
    }

    /**
     * Stores a measurement.
     *
     * @param string $prefix
     * the key prefix by which the measurements will be retrieved from the storage
     * @param string $key
     * the key
     * @param float $value
     * the value
     * @return void
     */
    abstract public function storeMeasurement($prefix, $key, $value);

    /**
     * Increments a measurement, starting with 1 if it doesn't exist yet.
     * @param string $prefix
     * the key prefix by which the measurements will be retrieved from the storage
     * @param string $key
     * the key
     * @return void
     */
    abstract public function incrementMeasurement($prefix, $key);

    /**
     * Gets all measurements.
     *
     * @param string $prefix
     * the key prefix by which the measurements will be retrieved from the storage
     * @param array $keys
     * the keys to retrieve
     * @param string $defaultValue
     * the default value a key gets if there is no value for it in the storage
     * @return array
     * the map with the keys and values
     */
    abstract public function getMeasurements($prefix, array $keys, $defaultValue = 'Nan');

}
