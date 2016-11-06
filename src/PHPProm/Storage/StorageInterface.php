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

interface StorageInterface {

    public function storeMeasurement($prefix, $key, $value);

    public function getMeasurements($prefix, array $keys, $defaultValue = 'Nan');

}
