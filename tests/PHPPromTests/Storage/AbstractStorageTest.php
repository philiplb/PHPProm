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

abstract class AbstractStorageTest extends \PHPUnit_Framework_TestCase {

    protected $storage;

    abstract protected function getRawKey($key);

    public function testStoreMeasurement() {
        $this->storage->storeMeasurement('metric', 'key', 42);
        $read = $this->getRawKey('metric:key');
        $expected = 42;
        $this->assertSame($expected, $read);
    }

    public function testIncrementMeasurement() {
        $this->storage->incrementMeasurement('metric', 'incrementKey');
        $read = $this->getRawKey('metric:incrementKey');
        $expected = 1;
        $this->assertSame($expected, $read);
        $this->storage->incrementMeasurement('metric', 'incrementKey');
        $read = $this->getRawKey('metric:incrementKey');
        $expected = 2;
        $this->assertSame($expected, $read);

    }

    public function testGetMeasurement() {
        $this->storage->storeMeasurement('metric', 'key', 42);
        $read = $this->storage->getMeasurements('metric', ['key', 'anotherKey'], 'Foo');
        $expected = [
            'key' => 42.0,
            'anotherKey' => 'Foo'
        ];
        $this->assertSame($expected, $read);
    }

    public function testAddAvailableMetricGetAvailableMetrics() {
        $read = $this->storage->getAvailableMetrics();
        $expected = [];
        $this->assertSame($expected, $read);
        $this->storage->addAvailableMetric('metric', 'label', 'help', 'type', 'defaultValue');
        $read = $this->storage->getAvailableMetrics();
        $expected = [
            [
                'metric' => 'metric',
                'label' => 'label',
                'help' => 'help',
                'type' => 'type',
                'defaultValue' => 'defaultValue'
            ]
        ];
        $this->assertSame($expected, $read);
        $this->storage->addAvailableMetric('metric2', 'label2', 'help2', 'type2', 'defaultValue2');
        $read = $this->storage->getAvailableMetrics();
        $expected[] = [
            'metric' => 'metric2',
            'label' => 'label2',
            'help' => 'help2',
            'type' => 'type2',
            'defaultValue' => 'defaultValue2'
        ];
        $this->assertSame($expected, $read);
    }

}
