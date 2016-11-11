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

use PHPProm\Storage\Memcached;

class MemcachedTest extends AbstractStorageTest {

    protected $memcached;

    protected function setUp() {
        $this->storage = new Memcached('localhost');
        $this->memcached = new \Memcached();
        $this->memcached->addServer('localhost', 11211);
        $this->memcached->delete('PHPProm:prefix:key');
        $this->memcached->delete('PHPProm:prefix:incrementKey');
    }

    protected function getRawKey($key) {
        return $this->memcached->get('PHPProm:'.$key);
    }

}