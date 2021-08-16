<?php

namespace Dealroadshow\K8S\Framework\Util;

use Dealroadshow\K8S\Data\TypedLocalObjectReference;
use Dealroadshow\K8S\Framework\Core\ManifestReference;
use Dealroadshow\K8S\Framework\Registry\AppRegistry;

class ManifestReferenceUtil
{
    public function __construct(private AppRegistry $appRegistry)
    {
    }

    public function toTypedLocalObjectReference(ManifestReference $manifestReference): TypedLocalObjectReference
    {
        $app = $this->appRegistry->get($manifestReference->appAlias());
        $class = new \ReflectionClass($manifestReference->className());
        $name = $app->namesHelper()->byManifestClass($class->getName());
        $kind = $class->getMethod('kind')->invoke(null);

        $objectReference = new TypedLocalObjectReference($kind, $name);
        if ($apiGroup = $manifestReference->apiGroup()) {
            $objectReference->setApiGroup($apiGroup);
        }

        return $objectReference;
    }
}
