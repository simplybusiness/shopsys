<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

interface MultiDomainEntityClassProviderInterface
{

    /**
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array;

    /**
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array;
}
