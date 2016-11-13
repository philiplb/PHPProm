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

/**
 * Class PrometheusExport
 * To export the measurements into the Prometheus format.
 * @package PHPProm
 */
class PrometheusExport {

    /**
     * Gets the header of a metric like its type or help.
     *
     * @param $type
     * the header type, "HELP" or "TYPE"
     * @param $metric
     * the metric
     * @param $label
     * the label of the header like the actual help text or Prometheus type
     *
     * @return string
     * the complete header
     */
    protected function getHeader($type, $metric, $label) {
        $header = '';
        if ($label !== null) {
            $header .= '# '.$type.' '.$metric.' '.$label."\n";
        }
        return $header;
    }

    /**
     * Gets a metric with header and values.
     *
     * @param string $metric
     * the metric
     * @param string $label
     * the categorizing label
     * @param array $labelsToValues
     * each label value mapping to the metric value
     * @param string $help
     * a helping text about the metric
     * @param string $type
     * the Prometheus type of the metric
     *
     * @return string
     * the Prometheus export string of this metric
     */
    protected function getMetric($metric, $label, array $labelsToValues, $help, $type) {
        $result  = $this->getHeader('HELP', $metric, $help);
        $result .= $this->getHeader('TYPE', $metric, $type);
        $result .= implode("\n", array_map(function($value, $labelValue) use ($metric, $label) {
            return $metric.'{'.$label.'="'.$labelValue.'"} '.$value;
        }, $labelsToValues, array_keys($labelsToValues)));
        return $result."\n";
    }

    /**
     * Gets a Prometheus export of the given storage.
     *
     * @param AbstractStorage $storage
     * the storage to export
     * @param $keys
     * the measurement keys to export
     *
     * @return string
     * the Prometheus export string of all available metrics
     */
    public function getExport(AbstractStorage $storage, $keys) {
        $export = '';
        foreach ($storage->getAvailableMetrics() as $availableMetric) {
            $measurements = $storage->getMeasurements($availableMetric['metric'], $keys, $availableMetric['defaultValue']);
            $export      .= $this->getMetric($availableMetric['metric'], $availableMetric['label'], $measurements, $availableMetric['help'], $availableMetric['type']);
        }
        return $export;
    }

}
