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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

class SilexSetup {

    protected function setupMiddleware(Application $app, AbstractStorage $storage) {

        $routeTime = new StopWatch($storage);

        $app->before(function (Request $request, Application $app) use ($routeTime) {
            $routeTime->start();
        }, Application::EARLY_EVENT);

        $app->finish(function (Request $request, Response $response) use ($routeTime, $storage) {
            $route = $request->get('_route');
            $routeTime->stop('time', $route);
            $storage->storeMeasurement('memory', $route, memory_get_peak_usage(true));
        });

    }

    public function setupAndGetMetricsRoute(Application $app, AbstractStorage $storage) {

        $this->setupMiddleware($app, $storage);

        return function() use ($app, $storage) {
            $routes = [];
            foreach ($app['routes']->all() as $route) {
                $path = str_replace('/', '_', $route->getPath());
                foreach ($route->getMethods() as $method) {
                    $routes[] = $method.$path;
                }
            }
            $export = new PrometheusExport();

            $routesTime = $storage->getMeasurements('time', $routes);
            $response = $export->getMetric('route_time', 'name', $routesTime, 'request times per route in seconds', 'gauge');

            $routesMemory = $storage->getMeasurements('memory', $routes);
            $response .= $export->getMetric('route_memory', 'name', $routesMemory, 'request memory per route in bytes', 'gauge');

            return new Response($response, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
        };
    }

}