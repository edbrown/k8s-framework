<?php

namespace Dealroadshow\K8S\Framework\Core\Ingress\IngressRule;

final class PathType
{
    private const EXACT = 'Exact';
    private const PREFIX = 'Prefix';
    private const IMPLEMENTATION_SPECIFIC = 'ImplementationSpecific';

    private function __construct(private string $pathType)
    {
    }

    public function toString(): string
    {
        return $this->pathType;
    }

    public static function exact(): self
    {
        return new self(self::EXACT);
    }

    public static function prefix(): self
    {
        return new self(self::PREFIX);
    }

    public static function implementationSpecific(): self
    {
        return new self(self::IMPLEMENTATION_SPECIFIC);
    }
}
