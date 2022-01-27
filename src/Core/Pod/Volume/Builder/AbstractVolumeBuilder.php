<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\Core\Pod\Volume\Builder;

use Dealroadshow\K8S\Data\Volume;

abstract class AbstractVolumeBuilder implements VolumeBuilderInterface
{
    /**
     * @internal
     *
     * @param Volume $volume
     */
    public function init(Volume $volume): void
    {
    }
}
