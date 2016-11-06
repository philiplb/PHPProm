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

abstract class AbstractStorage {

    protected $availableMetrics;

    public function __construct() {
        $this->availableMetrics = [];
    }

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

    public function getAvailableMetrics() {
        return $this->availableMetrics;
    }

    /**
     * @param $prefix
     * @param $key
     * @param $value
     * @return void
     */
    abstract public function storeMeasurement($prefix, $key, $value);

    /**
     * @param $prefix
     * @param $key
     * @return void
     */
    abstract public function incrementMeasurement($prefix, $key);

    /**
     * @param $prefix
     * @param array $keys
     * @param string $defaultValue
     * @return array
     */
    abstract public function getMeasurements($prefix, array $keys, $defaultValue = 'Nan');

}
