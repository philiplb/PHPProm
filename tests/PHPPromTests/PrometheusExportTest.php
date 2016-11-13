<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPPromTests;

use Eloquent\Phony\Phpunit\Phony;
use PHPProm\PrometheusExport;

class PrometheusExportTest extends \PHPUnit_Framework_TestCase {

    public function testGetExport() {
        $storageHandle = Phony::mock('\\PHPProm\\Storage\\AbstractStorage');
        $storageHandle->getMeasurements->returns(['val1' => 1, 'val2' => 2]);
        $storageHandle->getAvailableMetrics->returns([
            [
                'name' => 'name',
                'metric' => 'metric',
                'label' => 'label',
                'help' => 'help',
                'type' => 'type',
                'defaultValue' => 'defaultValue'
            ]
        ]);
        $storageMock = $storageHandle->get();
        $prometheusExport = new PrometheusExport();
        $read = $prometheusExport->getExport($storageMock, ['val1', 'val2', 'val3']);
        $expected = "# HELP metric help\n# TYPE metric type\nmetric{label=\"val1\"} 1\nmetric{label=\"val2\"} 2\n";
        $this->assertSame($expected, $read);
    }

}