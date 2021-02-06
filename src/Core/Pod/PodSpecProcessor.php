<?php

namespace Dealroadshow\K8S\Framework\Core\Pod;

use Dealroadshow\K8S\Data\PodSpec;
use Dealroadshow\K8S\Framework\App\AppInterface;
use Dealroadshow\K8S\Framework\Core\Container\ContainerInterface;
use Dealroadshow\K8S\Framework\Core\Container\ContainerMakerInterface;
use Dealroadshow\K8S\Framework\Core\Pod\Affinity\AffinityConfigurator;
use Dealroadshow\K8S\Framework\Core\Pod\ImagePullSecrets\ImagePullSecretsConfigurator;
use Dealroadshow\K8S\Framework\Core\Pod\Volume\VolumesConfigurator;
use Dealroadshow\K8S\Framework\Util\ClassName;

class PodSpecProcessor
{
    private ContainerMakerInterface $containerMaker;

    public function __construct(ContainerMakerInterface $containerMaker)
    {
        $this->containerMaker = $containerMaker;
    }

    public function process(PodSpecInterface $builder, PodSpec $spec, AppInterface $app): void
    {
        $affinity = new AffinityConfigurator($spec->affinity());
        $builder->affinity($affinity);

        $volumes = new VolumesConfigurator($spec->volumes(), $app);
        $builder->volumes($volumes);

        foreach ($builder->containers() as $containerBuilder) {
            $this->ensureValidContainerBuilder($containerBuilder);
            $container = $this->containerMaker->make($containerBuilder, $spec->volumes(), $app);
            $spec->containers()->add($container);
        }

        if (0 === $spec->containers()->count()) {
            throw new \LogicException(
                sprintf(
                    'No containers were returned from method %s::containers()',
                    ClassName::real($builder)
                )
            );
        }

        foreach ($builder->initContainers() as $containerBuilder) {
            $this->ensureValidContainerBuilder($builder);
            $container = $this->containerMaker->make($containerBuilder, $spec->volumes(), $app);
            $spec->initContainers()->add($container);
        }

        $imagePullSecrets = new ImagePullSecretsConfigurator($spec->imagePullSecrets(), $app);
        $builder->imagePullSecrets($imagePullSecrets);

        $builder->nodeSelector($spec->nodeSelector());

        $restartPolicy = $builder->restartPolicy();
        if (null !== $restartPolicy) {
            $spec->setRestartPolicy($restartPolicy->toString());
        }

        $priorityClassName = $builder->priorityClassName();
        if (null !== $priorityClassName) {
            $spec->setPriorityClassName($priorityClassName);
        }

        $builder->configurePodSpec($spec);
    }

    private function ensureValidContainerBuilder(mixed $builder): void
    {
        if (!$builder instanceof ContainerInterface) {
            throw new \TypeError(
                sprintf('All containers must be instances of "%s"', ContainerInterface::class)
            );
        }
    }
}
