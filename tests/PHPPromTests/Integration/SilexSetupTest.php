<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPPromTests\Integration;

use PHPProm\Storage\Memcached;
use Silex\WebTestCase;

class SilexSetupTest extends WebTestCase {

    protected function setUp() {
        parent::setUp();
        $keys = [
            'PHPProm:time:GET_metrics',
            'PHPProm:memory:GET_metrics',
            'PHPProm:requests_total:GET_metrics',
            'PHPProm:time:GET_test1',
            'PHPProm:memory:GET_test1',
            'PHPProm:requests_total:GET_test1',
            'PHPProm:time:GET_test2',
            'PHPProm:memory:GET_test2',
            'PHPProm:requests_total:GET_test2'
        ];
        $memcached = new \Memcached();
        $memcached->addServer('localhost', 11211);
        foreach ($keys as $key) {
            $memcached->delete($key);
        }
    }

    public function createApplication() {
        $app = new \Silex\Application();

        $app['debug'] = true;
        unset($app['exception_handler']);

        $storage = new Memcached('localhost');
        $silexSetup = new \PHPProm\Integration\SilexSetup();
        $app->get('/metrics', $silexSetup->setupAndGetMetricsRoute($app, $storage));

        $app->get('/test1', function() {
            sleep(1);
            return 'ok';
        });
        $app->get('/test2', function() {
            return 'ok';
        });
        $app->get('/test3', function() {
            return 'ok';
        });

        return $app;
    }

    public function testMeasurements() {

        $client = $this->createClient();
        $client->request('GET', '/test1');
        $client->request('GET', '/test2');
        $client->request('GET', '/test2');

        $client->request('GET', '/metrics');
        $response = $client->getResponse();
        $this->assertTrue($response->isOk());
        $content = $response->getContent();

        $this->assertRegExp('/# HELP route\\_time request times per route in seconds/', $content);
        $this->assertRegExp('/# TYPE route\\_time gauge/', $content);
        $this->assertRegExp('/route_time{name="GET_metrics"} Nan/', $content);
        $this->assertRegExp('/route_time{name="GET_test1"} [0-9]+/', $content);
        $this->assertRegExp('/route_time{name="GET_test2"} [0-9]+/', $content);
        $this->assertRegExp('/route_time{name="GET_test3"} Nan+/', $content);

        $this->assertRegExp('/# HELP route\\_memory request memory per route in bytes/', $content);
        $this->assertRegExp('/# TYPE route\\_memory gauge/', $content);
        $this->assertRegExp('/route_memory{name="GET_metrics"} Nan/', $content);
        $this->assertRegExp('/route_memory{name="GET_test1"} [0-9]+/', $content);
        $this->assertRegExp('/route_memory{name="GET_test2"} [0-9]+/', $content);
        $this->assertRegExp('/route_memory{name="GET_test3"} Nan+/', $content);

        $this->assertRegExp('/# HELP route\\_requests_total total requests per route/', $content);
        $this->assertRegExp('/# TYPE route\\_requests_total counter/', $content);
        $this->assertRegExp('/route_requests_total{name="GET_metrics"} 0/', $content);
        $this->assertRegExp('/route_requests_total{name="GET_test1"} 1/', $content);
        $this->assertRegExp('/route_requests_total{name="GET_test2"} 2/', $content);
        $this->assertRegExp('/route_requests_total{name="GET_test3"} 0/', $content);

    }
}