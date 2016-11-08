<?php

namespace PHPPromTests\Storage;

use PHPProm\Storage\Redis;

class RedisTest extends AbstractStorageTest {

    protected $redis;

    protected function setUp() {
        $this->storage = new Redis('localhost', '', 6379, 'PHPProm:', 0);
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
        $this->redis->setOption(\Redis::OPT_PREFIX, 'PHPProm:');
        $this->redis->delete('prefix:key');
        $this->redis->delete('prefix:incrementKey');
    }

    protected function getRawKey($key) {
        return (int)$this->redis->get(substr($key, strlen('PHPProm:')));
    }

}