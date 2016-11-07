<?php

namespace PHPPromTests\Storage;

abstract class AbstractStorageTest extends \PHPUnit_Framework_TestCase {

    protected $storage;

    abstract protected function getRawKey($key);

    public function testStoreMeasurement() {
        $this->storage->storeMeasurement('prefix', 'key', 42);
        $read = $this->getRawKey('PHPProm:prefix:key');
        $expected = 42;
        $this->assertSame($expected, $read);
    }

    public function testIncrementMeasurement() {
        $this->storage->incrementMeasurement('prefix', 'incrementKey');
        $read = $this->getRawKey('PHPProm:prefix:incrementKey');
        $expected = 1;
        $this->assertSame($expected, $read);
        $this->storage->incrementMeasurement('prefix', 'incrementKey');
        $read = $this->getRawKey('PHPProm:prefix:incrementKey');
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

}
