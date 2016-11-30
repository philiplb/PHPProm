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

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Slim\App;
use PHPProm\PrometheusExport;
use PHPProm\Storage\AbstractStorage;
use PHPProm\StopWatch;
use Slim\Route;

/**
 * Class Slim3Setup
 * Setups Slim applications to measure:
 * - the time of each route
 * - the used memory of each route
 * - the amount of requests of each route
 * It also offers an function to be used for a Prometheus scrapable endpoint.
 * @package PHPProm\Integration
 */
class Slim3Setup {


    /**
     * Sets up the Slim middleware where the actual measurements happen.
     *
     * @param App $app
     * the Silex application
     * @param AbstractStorage $storage
     * the storage for the measurements
     */
    protected function setupMiddleware(App $app, AbstractStorage $storage) {
        $storage->addAvailableMetric('route_time', 'name', 'request times per route in seconds', 'gauge', 'Nan');
        $storage->addAvailableMetric('route_memory', 'name', 'request memory per route in bytes', 'gauge', 'Nan');
        $storage->addAvailableMetric('route_requests_total', 'name', 'total requests per route', 'counter', 0);

        $app->add(function(Request $request, Response $response, App $next) use ($storage) {
            $method    = $request->getMethod();
            $pattern   = str_replace('/', '_', $request->getAttribute('route')->getPattern());
            $route     = $method.$pattern;
            $routeTime = new StopWatch($storage);
            $routeTime->start();
            $response = $next($request, $response);
            $routeTime->stop('route_time', $route);
            $storage->storeMeasurement('route_memory', $route, memory_get_peak_usage(true));
            $storage->incrementMeasurement('route_requests_total', $route);
            return $response;
        });
    }

    /**
     * Gets the path with all methods from a route.
     *
     * @param Route $route
     * the route to get the path and methods from
     * @return array
     * the pathes with methods
     */
    protected function getPathWithMethods(Route $route) {
        $routes  = [];
        $pattern = str_replace('/', '_', $route->getPattern());
        $methods = $route->getMethods();
        foreach ($methods as $method) {
            $routes[] = $method.$pattern;
        }
        return $routes;
    }

    /**
     * Sets up the Slim middlewares where the actual measurements happen
     * and returns a function to be used for a Prometheus scrapable endpoint.
     *
     * @param App $app
     * the Slim application
     * @param AbstractStorage $storage
     * the storage for the measurements
     *
     * @return \Closure
     * the function to be used for a Prometheus scrapable endpoint
     */
    public function setupAndGetMetricsRoute(App $app, AbstractStorage $storage) {
        $this->setupMiddleware($app, $storage);
        $self = $this;
        return function(Request $request, Response $response) use ($app, $storage, $self) {
            $routes          = [];
            $availableRoutes = $app->getContainer()->get('router')->getRoutes();
            foreach ($availableRoutes as $route) {
                $routes = array_merge($routes, $self->getPathWithMethods($route));
            }
            $export       = new PrometheusExport();
            $responseBody = $export->getExport($storage, $routes);

            $response = $response->withHeader('Content-type', 'text/plain; version=0.0.4');
            $response->getBody()->write($responseBody);

        };
    }
}
