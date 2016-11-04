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

class PrometheusExport {

    protected function getHeader($type, $metric, $label) {
        $header = '';
        if ($label !== null) {
            $header .= '# '.$type.' '.$metric.' '.$label."\n";
        }
        return $header;
    }

    public function getMetric($metric, $label, array $labelsToValues, $help = null, $type = null) {
        $result = $this->getHeader('HELP', $metric, $help);
        $result .= $this->getHeader('TYPE', $metric, $type);
        $result .= implode("\n", array_map(function ($value, $labelValue) use ($metric, $label) {
            return $metric.'{'.$label.'="'.$labelValue.'"} '.$value;
        }, $labelsToValues, array_keys($labelsToValues)));
        return $result."\n";
    }

}