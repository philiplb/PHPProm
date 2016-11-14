PHPProm
-------

PHPProm is a library to measure some performance relevant metrics and expose them for Prometheus.

Its goal is to offer a simple, drop in solution to start measuring but
without limiting customization.

As the measurements are regulary collected by Prometheus visiting a
specific endpoint, they need to be stored. PHPProm offeres support
for various backends like Redis or Memcached.

![Grafana Sample](docs/grafana.png)

## Documentation

- [API Documentation 0.1.0](http://philiplb.github.io/PHPProm/docs/0.1.0/) (upcoming)


## Package

PHPProm uses [SemVer](http://semver.org/) for versioning. Currently,
the API changes quickly due to be < 1.0.0, so take care about notes
in the changelog when upgrading.

### Stable

Upcoming.

### Bleeding Edge

```json
"require": {
    "philiplb/crudlex": "0.1.x-dev"
}
```

## Getting Started

Here is an example about how to quickly get started measuring a Silex
application using Redis as Storage:

### Prerequisites

You need to have the
[PHP redis extension](https://pecl.php.net/package/redis) installed.

And you need to have a Redis server up and running. Here we just
assume "localhost" as host and "supersecret" as authentication password. 

### Require PHPProm

The PHPProm package is integrated via composer:

```bash
composer require "philiplb/phpprom"
```

### Setup the Silex Application

The first step is to create a storage object:

```PHP
$storage = new PHPProm\Storage\Redis('localhost', 'supersecret');
```

With this, the Silex setup can be called. It returns a function
to be used as metrics route which can be scraped by Prometheus:

```PHP
$silexPrometheusSetup = new PHPProm\Integration\SilexSetup();
$metricsAction = silexPrometheusSetup->setupAndGetMetricsRoute($app, $storage);
$app->get('/metrics', $metricsAction);
```

## Integrations

Integrating some Prometheus scrapable metrics should be as easy as
possible. So some setup steps of frameworks are abstracted away into integrations in order to make PHPProm a drop in solution.

More integrations are to come. If you have a specific request, just
drop me a line. Or make a pull request. :)

### Silex

The [Silex](http://silex.sensiolabs.org/) integration measures the
following metrics:

- Time spent per route as gauge
- Consumed memory per route as gauge
- How often each route has been called as counter

It requires the package ["silex/silex"](https://packagist.org/packages/silex/silex).

The integration is represented by the class
_PHPProm\Integration\SilexSetup_ with it's usage explained in the
"Getting Started" section.

Adding more metrics is easy via the function _addAvailableMetric_ of
the storage instance. See the subchapter for custom integrations for
a detailed explanation of the parameters.

The actual measurements are added via the according storage instance
functions (see again the custom integrations subchapter). All data 
automatically appears within the metrics endpoint.

### Custom Integration

Writing a custom integration consists of three parts. First, the metrics have to be setup, second measurements needs to happen and third, a Prometheus scrapable metrics endpoint has to be offered.

First, the metrics to measure have to be added to the storage
instance via the method _addAvailableMetric_:

```PHP
$storage->addAvailableMetric(
	$metric, // the Prometheus metric name itself
	$label, // the name of the one Prometheus label to categorize the values
	$help, // a small, meaningful help text for the metric
	$type, // the Prometheus type of the metric like "gauge" or "counter"
	$defaultValue // the default value to be taken if no measurement happened yet for the metric/label combination, "Nan" for example or "0"
);
```

Now, the measurements have to happen. The storage object offers two
methods for this:

- _storeMeasurement($metric, $key, $value):_ to store a raw value for a
  metric under the given key
- _incrementMeasurement($metric, $key):_ increments a counter for the
  metric under the given key, starts with 1 if it didn't exist before

There is a little helper class to measure time, the _PHPProm\StopWatch_. To start the measurement, call its function
_start()_ and to stop and store the measurement, call the function
_stop($metric, $key)_. The parameters have the same meaning as the
storage function parameters.

The third part is to offer an endpoint delivering the metrics. To get
the content, the class _PHPProm\PrometheusExport_ exists. It has a
single public function _getExport(AbstractStorage $storage, $keys)_
where the storage instance is handed in along with all expected keys.
The function returns a string with all the Prometheus data to be used
as response in the endpoint. It should be delivered with the
"Content-Type: text/plain; version=0.0.4".

## Storage Implementations

There are several storage implementations available for the
measurements so the metrics endpoint can deliver them. It is also easy 
to write an own one if the existing ones don't cover the use case.
They are all in the namespace _PHPProm\Storage_.

### Redis

The Redis storage needs to have the [PHP redis extension](https://pecl.php.net/package/redis) installed. Its constructor takes the
following parameters:

- _string $host:_ the connection host
- _null|string $password:_ the password for authentication, null to ignore
- _int $port:_ the connection port, default 6379
- _string $prefix:_ the global key prefix to use, default 'PHPProm:'
- _null|string $dbIndex:_ the Redis DB index to use, null to ignore

It is very fast and offers persistence, so this one is the recommended
storage implementation.

### Memcached

The Memcached storage implementation needs to have the [PHP memcached extension](http://php.net/manual/en/book.memcached.php) installed. Its
constructor takes the following parameters:

- _string $host:_ the connection host
- _int $port:_ the connection port, default 11211
- _string $prefix:_ the global key prefix to use, default 'PHPProm:'

This storage implementation is even faster then Redis, but offers no
persistence and so is not recommended if there are counters measured
over time for example which should not be lost.

### DBAL

The DBAL storage implementation needs to have the package
"doctrine/dbal" and the prerequisites of the used driver must be
fullfilled. Currently, the MySQL, PostgreSQL and SQLite drivers have
been tested. But the SQL statements have been kept simple in order to
be compatible with many of the DBAL supported databases. Give me a
shout if you find something not working.

Its constructor takes the following parameters:

- _\Doctrine\DBAL\Connection $connection:_ the DBAL connection
- _string $table:_ the table to use

The MySQL scheme of the table is:

```SQL
 CREATE TABLE `phpprom` (
     `key` varchar(255) NOT NULL,
     `value` double NOT NULL,
     PRIMARY KEY (`key`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

The SQLite scheme of the table is:

```SQL
CREATE TABLE `phpprom` (
	`key`	TEXT NOT NULL UNIQUE,
	`value`	REAL NOT NULL,
	PRIMARY KEY(`key`)
);
```

The PostgreSQL scheme of the table is:

```SQL
CREATE TABLE public.phpprom (
    key VARCHAR(255) PRIMARY KEY NOT NULL,
    value DOUBLE PRECISION NOT NULL
);
CREATE UNIQUE INDEX phpprom_key_uindex ON public.phpprom (key);
```

This one is possibly the slowest one, but offers a secure data storage
and is mostly available in existing stacks.

### Custom

In case you want to store the measurements in a different backend,
you can inherit your implementation from _PHPProm\Storage\AbstractStorage_ and implement the abstract methods:

- _abstract public function storeMeasurement($metric, $key, $value):_
  Stores a measurement.
- _abstract public function incrementMeasurement($metric, $key):_
  Increments a measurement, starting with 1 if it doesn't exist yet.
- _abstract public function getMeasurements($metric, array $keys, $defaultValue = 'Nan'):_
  Gets all measurements.

New storage implementations would make a good pull request again. :)

## Status

[![Build Status](https://travis-ci.org/philiplb/PHPProm.svg?branch=master)](https://travis-ci.org/philiplb/PHPProm)
[![Coverage Status](https://coveralls.io/repos/philiplb/PHPProm/badge.png?branch=master)](https://coveralls.io/r/philiplb/PHPProm?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e0026986-7a55-4a9f-9086-53abe1918556/mini.png)](https://insight.sensiolabs.com/projects/e0026986-7a55-4a9f-9086-53abe1918556)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/philiplb/PHPProm/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/philiplb/PHPProm/?branch=master)

[![Total Downloads](https://poser.pugx.org/philiplb/phpprom/downloads.svg)](https://packagist.org/packages/philiplb/phpprom)
[![Latest Stable Version](https://poser.pugx.org/philiplb/phpprom/v/stable.svg)](https://packagist.org/packages/phpprom/crudlex)
[![Latest Unstable Version](https://poser.pugx.org/philiplb/phpprom/v/unstable.svg)](https://packagist.org/packages/phpprom/crudlex) [![License](https://poser.pugx.org/philiplb/phpprom/license.svg)](https://packagist.org/packages/philiplb/phpprom)
