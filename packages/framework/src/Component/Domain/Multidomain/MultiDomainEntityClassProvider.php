<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

class MultiDomainEntityClassProvider implements MultiDomainEntityClassProviderInterface
{
    /**
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array
    {
        return [];
    }
}
