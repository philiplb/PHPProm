<?php

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