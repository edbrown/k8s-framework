<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\Middleware;

use Dealroadshow\K8S\Framework\Core\ManifestInterface;

interface ManifestMethodMiddlewareInterface
{
    public const NO_RETURN_VALUE = 'MANIFEST_MIDDLEWARE_NO_RETURN_VALUE';

    public function supports(ManifestInterface $manifest, string $methodName, array $params): bool;
    public static function priority(): int;
}
