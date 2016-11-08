<?php

namespace PHPPromTests\Storage;

abstract class AbstractStorageTest extends \PHPUnit_Framework_TestCase {

    protected $storage;

    abstract protected function getRawKey($key);

    public function testStoreMeasurement() {
        $this->storage->storeMeasurement('prefix', 'key', 42);
        $read = $this->getRawKey('prefix:key');
        $expected = 42;
        $this->assertSame($expected, $read);
    }

    public function testIncrementMeasurement() {
        $this->storage->incrementMeasurement('prefix', 'incrementKey');
        $read = $this->getRawKey('prefix:incrementKey');
        $expected = 1;
        $this->assertSame($expected, $read);
        $this->storage->incrementMeasurement('prefix', 'incrementKey');
        $read = $this->getRawKey('prefix:incrementKey');
        $expected = 2;
        $this->assertSame($expected, $read);

    }

    public function testGetMeasurement() {
        $this->storage->storeMeasurement('prefix', 'key', 42);
        $read = $this->storage->getMeasurements('prefix', ['key', 'anotherKey'], 'Foo');
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
        $this->storage->addAvailableMetric('storagePrefix', 'metric', 'label', 'help', 'type', 'defaultValue');
        $read = $this->storage->getAvailableMetrics();
        $expected = [
            [
                'storagePrefix' => 'storagePrefix',
                'metric' => 'metric',
                'label' => 'label',
                'help' => 'help',
                'type' => 'type',
                'defaultValue' => 'defaultValue'
            ]
        ];
        $this->assertSame($expected, $read);
        $this->storage->addAvailableMetric('storagePrefix2', 'metric2', 'label2', 'help2', 'type2', 'defaultValue2');
        $read = $this->storage->getAvailableMetrics();
        $expected[] = [
            'storagePrefix' => 'storagePrefix2',
            'metric' => 'metric2',
            'label' => 'label2',
            'help' => 'help2',
            'type' => 'type2',
            'defaultValue' => 'defaultValue2'
        ];
        $this->assertSame($expected, $read);
    }

}
