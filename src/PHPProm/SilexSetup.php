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

        $app->finish(function (Request $request, Response $response) use ($routeTime) {
            $route = $request->get('_route');
            $routeTime->stop($route);
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
            $measurements = $storage->getMeasurements($routes);

            $export = new PrometheusExport();
            $response = $export->getMetric('route', 'name', $measurements, 'request times', 'gauge');

            return new Response($response, 200, ['Content-Type' => 'text/plain; version=0.0.4']);
        };
    }

}