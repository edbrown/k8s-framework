<?php

namespace Dealroadshow\K8S\Framework\Monitoring\Prometheus\ServiceMonitor;

use Dealroadshow\K8S\Data\Collection\StringList;
use Dealroadshow\K8S\Framework\Monitoring\Prometheus\Configurator\EndpointsConfigurator;
use Dealroadshow\K8S\Framework\Monitoring\Prometheus\MonitorInterface;

interface ServiceMonitorInterface extends MonitorInterface
{
    public function endpoints(EndpointsConfigurator $endpoints): void;
    public function targetLabels(StringList $labels): void;
}
