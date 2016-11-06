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

/**
 * Class StopWatch
 * Small utility class to measure the time of something.
 * @package PHPProm
 */
class StopWatch {

    /**
     * @var AbstractStorage
     * the storage to store the result at
     */
    protected $storage;

    /**
     * @var float
     * the moment the measurement started
     */
    protected $start;

    /**
     * StopWatch constructor.
     * @param AbstractStorage $storage
     * the storage to store the result at
     */
    public function __construct(AbstractStorage $storage) {
        $this->storage = $storage;
    }

    /**
     * To start the measurement.
     */
    public function start() {
        $this->start = microtime(true);
    }

    /**+
     * To stop and store the measurement as float seconds.
     *
     * @param string $prefix
     * the key prefix by which the measurements will be retrieved from the storage
     * @param string $key
     * the key
     */
    public function stop($prefix, $key) {
        $time = microtime(true) - $this->start;
        $this->storage->storeMeasurement($prefix, $key, $time);
    }

}
