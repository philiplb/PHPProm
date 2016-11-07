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
use PHPProm\StopWatch;

class StopWatchTest extends \PHPUnit_Framework_TestCase{

    public function testStartStop() {
        $storageHandle = Phony::mock('\\PHPProm\\Storage\\AbstractStorage');
        $storageMock   = $storageHandle->get();

        $stopWatch = new StopWatch($storageMock);
        $stopWatch->start();
        sleep(1);
        $stopWatch->stop('prefix', 'key');

        $storageHandle->storeMeasurement->once()->called();
        $call      = $storageHandle->storeMeasurement->firstCall();
        $arguments = $call->arguments();
        $this->assertSame('prefix', $arguments->get(0));
        $this->assertSame('key', $arguments->get(1));
        $this->assertTrue(abs($arguments->get(2) - 1.0) < 0.01);
    }

}