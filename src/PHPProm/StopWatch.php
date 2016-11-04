<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPProm;

use PHPProm\Storage\AbstractStorage;

class StopWatch {

    protected $storage;

    protected $start;

    public function __construct(AbstractStorage $storage) {
        $this->storage = $storage;
    }

    public function start() {
        $this->start = microtime(true);
    }

    public function stop($key) {
        $time = microtime(true) - $this->start;
        $this->storage->storeMeasurement($time, $key);
    }

}