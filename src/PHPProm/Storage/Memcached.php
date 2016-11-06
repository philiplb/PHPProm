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

class Memcached implements StorageInterface {

    protected $memcached;

    protected $prefix;

    public function __construct($host, $port = 11211, $prefix = 'PHPProm:', $weight = 0) {
        $this->memcached = new \Memcached();
        $this->memcached->addServer($host, $port, $weight);
        $this->prefix = $prefix;
    }

    public function storeMeasurement($prefix, $key, $value) {
        $this->memcached->set($this->prefix.$prefix.':'.$key, $value);
    }

    public function incrementMeasurement($prefix, $key) {
        // Increment doesn't work on older versions, see
        // https://github.com/php-memcached-dev/php-memcached/issues/133
        $value = $this->memcached->get($this->prefix.$prefix.':'.$key);
        if ($value === false) {
            $value = 0;
        }
        $value++;
        $this->storeMeasurement($prefix, $key, $value);
    }

    public function getMeasurements($prefix, array $keys, $defaultValue = 'Nan') {
        $measurements = [];
        foreach ($keys as $key) {
            $measurements[$key] = $defaultValue;
        }
        $prefixedKeys = array_map(function($key) use ($prefix) {
            return $this->prefix.$prefix.':'.$key;
        }, $keys);
        foreach ($this->memcached->getMulti($prefixedKeys) as $key => $value) {
            $unprefixedKey = substr($key, strlen($this->prefix) + strlen($prefix) + 1);
            $measurements[$unprefixedKey] = $value !== false ? (float)$value : $defaultValue;
        }
        return $measurements;
    }
}
