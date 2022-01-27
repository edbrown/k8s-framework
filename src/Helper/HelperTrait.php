<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\Helper;

use Dealroadshow\K8S\Framework\App\AppInterface;

trait HelperTrait
{
    protected AppInterface $app;

    public function setApp(AppInterface $app): void
    {
        $this->app = $app;
    }
}
