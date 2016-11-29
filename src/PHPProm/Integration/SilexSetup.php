<?php

/*
 * This file is part of the PHPProm package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPProm\Integration;

use PHPProm\PrometheusExport;
use PHPProm\StopWatch;
use PHPProm\Storage\AbstractStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

/**
 * Class SilexSetup
 * Setups Silex applications to measure:
 * - the time of each route
 * - the used memory of each route
 * - the amount of requests of each route
 * It also offers an function to be used for a Prometheus scrapable endpoint.
 * @package PHPProm\Integration
 */
class SilexSetup {

    /**
     * Sets up the Silex middlewares where the actual measurements happen.
     *
     * @param Application $app
     * the Silex application
     * @param AbstractStorage $storage
     * the storage for the measurements
     */
    protected function setupMiddleware(Application $app, AbstractStorage $storage) {

        $storage->addAvailableMetric('route_time', 'name', 'request times per route in seconds', 'gauge', 'Nan');
        $storage->addAvailableMetric('route_memory', 'name', 'request memory per route in bytes', 'gauge', 'Nan');
        $storage->addAvailableMetric('route_requests_total', 'name', 'total requests per route', 'counter', 0);

        $routeTime = new StopWatch($storage);

        $app->before(function() use ($routeTime) {
            $routeTime->start();
        }, Application::EARLY_EVENT);

        $app->finish(function(Request $request) use ($routeTime, $storage) {
            $route = $request->get('_route');
            $routeTime->stop('route_time', $route);
            $storage->storeMeasurement('route_memory', $route, memory_get_peak_usage(true));
            $storage->incrementMeasurement('route_requests_total', $route);
        });

    }

    /**
     * Sets up the Silex middlewares where the actual measurements happen
     * and returns a function to be used for a Prometheus scrapable endpoint.
     *
     * @param Application $app
     * the Silex application
     * @param AbstractStorage $storage
     * the storage for the measurements
     *
     * @return \Closure
     * the function to be used for a Prometheus scrapable endpoint
     */
    public function setupAndGetMetricsRoute(Application $app, AbstractStorage $storage) {

        $this->setupMiddleware($app, $storage);

        return function() use ($app, $storage) {
            $routes = [];
            $supportedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'];
            foreach ($app['routes']->all() as $route) {
                $path        = str_replace('/', '_', $route->getPath());
                $foundMethod = false;
                foreach ($route->getMethods() as $method) {
                    $routes[] = $method.$path;
                    $foundMethod = true;
                }
                if (!$foundMethod) {
                    foreach ($supportedMethods as $supportedMethod) {
                        $routes[] = $supportedMethod.$path;
                    }
                }
            }
            $export   = new PrometheusExport();
            $response = $export->getExport($storage, $routes);
            return new Response($response, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
        };
    }

}
