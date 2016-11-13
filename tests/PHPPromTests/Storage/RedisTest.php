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

use PHPProm\Storage\Redis;

class RedisTest extends AbstractStorageTest {

    protected $redis;

    protected function setUp() {
        $this->storage = new Redis('localhost', '', 6379, 'PHPProm:', 0);
        $this->redis = new \Redis();
        $this->redis->connect('localhost');
        $this->redis->setOption(\Redis::OPT_PREFIX, 'PHPProm:');
        $this->redis->delete('metric:key');
        $this->redis->delete('metric:incrementKey');
    }

    protected function getRawKey($key) {
        return (int)$this->redis->get($key);
    }

}