<?php

declare(strict_types=1);

namespace Dealroadshow\K8S\Framework\ResourceMaker;

use Dealroadshow\K8S\API\Apps\StatefulSet;
use Dealroadshow\K8S\APIResourceInterface;
use Dealroadshow\K8S\Data\StatefulSetSpec;
use Dealroadshow\K8S\Framework\App\AppInterface;
use Dealroadshow\K8S\Framework\Core\ManifestInterface;
use Dealroadshow\K8S\Framework\Core\Pod\PodTemplateSpecProcessor;
use Dealroadshow\K8S\Framework\Core\StatefulSet\StatefulSetInterface;
use Dealroadshow\K8S\Framework\Core\StatefulSet\UpdateStrategy\UpdateStrategyConfigurator;
use Dealroadshow\K8S\Framework\Event\StatefulSetGeneratedEvent;
use Dealroadshow\K8S\Framework\Proxy\ManifestProxyFactory;
use Dealroadshow\K8S\Framework\Registry\AppRegistry;
use Dealroadshow\K8S\Framework\ResourceMaker\Traits\ConfigureSelectorTrait;

class StatefulSetMaker extends AbstractResourceMaker
{
    use ConfigureSelectorTrait;

    public function __construct(
        private AppRegistry $appRegistry,
        private PersistentVolumeClaimMaker $pvcMaker,
        private PodTemplateSpecProcessor $podSpecProcessor,
        private ManifestProxyFactory $proxyFactory
    ) {
    }

    protected function supportsClass(): string
    {
        return StatefulSetInterface::class;
    }

    protected function makeResource(ManifestInterface|StatefulSetInterface $manifest, AppInterface $app): APIResourceInterface
    {
        $serviceReference = $manifest->serviceName();
        $app = $this->appRegistry->get($serviceReference->appAlias());
        $serviceName = $app->namesHelper()->byServiceClass($serviceReference->className());

        $spec = new StatefulSetSpec($serviceName);
        $sts = new StatefulSet($spec);

        $this->configureSelector($manifest, $spec->selector());
        $app->metadataHelper()->configureMeta($manifest, $sts);
        $this->podSpecProcessor->process($manifest, $spec->template(), $app);

        foreach ($spec->selector()->matchLabels()->all() as $name => $value) {
            $sts->metadata()->labels()->add($name, $value);
            $spec->template()->metadata()->labels()->add($name, $value);
        }

        $podManagementPolicy = $manifest->podManagementPolicy();
        if (null !== $podManagementPolicy) {
            $spec->setPodManagementPolicy($podManagementPolicy->toString());
        }

        $spec
            ->setReplicas($manifest->replicas())
            ->setRevisionHistoryLimit($manifest->revisionHistoryLimit());

        $updateStrategy = new UpdateStrategyConfigurator($spec->updateStrategy());
        $manifest->updateStrategy($updateStrategy);

        foreach ($manifest->volumeClaimTemplates() as $template) {
            $template->setApp($app);
            if (!str_contains($template::class, '@anonymous')) {
                $template = $this->proxyFactory->makeProxy($template);
            }
            $pvc = $this->pvcMaker->make($template, $app);
            $pvc->metadata()->setName($template::shortName()); // Rewrite PVC full name to it's short name
            $spec->volumeClaimTemplates()->add($pvc);
        }

        $manifest->configureStatefulSet($sts);

        $this->dispatcher->dispatch(new StatefulSetGeneratedEvent($manifest, $sts, $app), StatefulSetGeneratedEvent::NAME);

        return $sts;
    }
}
