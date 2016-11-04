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

abstract class AbstractStorage {

    abstract public function storeMeasurement($prefix, $key, $value);

    abstract public function getMeasurements($prefix, array $keys);

}
