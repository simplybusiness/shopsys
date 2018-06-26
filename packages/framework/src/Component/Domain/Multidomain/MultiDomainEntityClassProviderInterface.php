<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

/**
 * Interface MultiDomainEntityClassProviderInterface
 *
 * Serves as provider for ignored and manual entities.
 *
 * MultidomainEntityClassFinder finds entities that has identifier called $domainId, but sometimes this assumption
 * is not implemented for some reason. This interface adds option to ignore or manually add entities that does not match
 * this assumption.
 */
interface MultiDomainEntityClassProviderInterface
{

    /**
     * Return entities FQNs that has identifier called $domainId but is not multi domain entity.
     *
     * @return string[]
     */
    public function getIgnoredMultidomainEntitiesNames(): array;

    /**
     * Return entities FQNs that has $domainId property, but property itself is not identifier.
     *
     * @return string[]
     */
    public function getManualMultidomainEntitiesNames(): array;
}
