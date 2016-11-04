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

class RedisStorage extends AbstractStorage {

    protected $redis;

    protected $prefix;

    public function __construct($host, $password = null, $port = 6379, $dbIndex = null, $prefix = 'RoutePerformanceExporter:') {
        $this->redis = new \Redis();
        $this->redis->connect($host, $port);
        if ($password !== null) {
            $this->redis->auth($password);
        }
        if ($dbIndex !== null) {
            $this->redis->select($dbIndex);
        }
        $this->redis->setOption(\Redis::OPT_PREFIX, $prefix);
        $this->prefix = $prefix;
    }

    public function storeMeasurement($prefix, $key, $value) {
        $this->redis->set($prefix.':'.$key, $value);
    }

    public function getMeasurements($prefix, array $keys) {
        $measurements = [];
        $prefixedKeys = array_map(function($key) use ($prefix) {
            return $prefix.':'.$key;
        }, $keys);
        foreach ($this->redis->mget($prefixedKeys) as $i => $value) {
            $measurements[$keys[$i]] = $value !== false ? (float)$value : 'Nan';
        }
        return $measurements;
    }
}
