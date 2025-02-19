<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\Core\Container\Resources;

use Dealroadshow\K8S\Data\Collection\StringOrFloatMap;
use Dealroadshow\K8S\Data\ResourceRequirements;

class ResourcesConfigurator implements ContainerResourcesInterface
{
    protected const CPU = 'cpu';
    protected const MEMORY = 'memory';
    protected const EPHEMERAL_STORAGE = 'ephemeral-storage';
    protected const STORAGE = 'storage';

    public function __construct(protected ResourceRequirements $resources)
    {
    }

    public function requestCPU(CPU $cpu): static
    {
        return $this->setCPU($cpu, $this->resources->requests());
    }

    public function requestMemory(Memory $memory): static
    {
        return $this->setMemory(self::MEMORY, $memory, $this->resources->requests());
    }

    public function requestStorage(Memory $memory): static
    {
        return $this->setMemory(self::EPHEMERAL_STORAGE, $memory, $this->resources->requests());
    }

    public function limitCPU(CPU $cpu): static
    {
        return $this->setCPU($cpu, $this->resources->limits());
    }

    public function limitMemory(Memory $memory): static
    {
        return $this->setMemory(self::MEMORY, $memory, $this->resources->limits());
    }

    public function limitStorage(Memory $memory): static
    {
        return $this->setMemory(self::EPHEMERAL_STORAGE, $memory, $this->resources->limits());
    }

    private function setCPU(CPU $cpu, StringOrFloatMap $map): static
    {
        $map->add(self::CPU, $cpu->toString());

        return $this;
    }

    protected function setMemory(string $key, Memory $memory, StringOrFloatMap $map): static
    {
        $map->add($key, $memory->toString());

        return $this;
    }
}
