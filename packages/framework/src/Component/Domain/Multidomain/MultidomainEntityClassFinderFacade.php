<?php

namespace Shopsys\FrameworkBundle\Component\Domain\Multidomain;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Doctrine\EntityNotNullableColumnsFinder;

class MultidomainEntityClassFinderFacade
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultidomainEntityClassFinder
     */
    protected $multidomainEntityClassFinder;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Multidomain\MultiDomainEntityClassProviderInterface
     */
    private $multiDomainEntityClassProvider;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\EntityNotNullableColumnsFinder
     */
    protected $entityNotNullableColumnsFinder;

    public function __construct(
        EntityManagerInterface $em,
        MultidomainEntityClassFinder $multidomainEntityClassFinder,
        MultiDomainEntityClassProviderInterface $multiDomainEntityClassProvider,
        EntityNotNullableColumnsFinder $entityNotNullableColumnsFinder
    ) {
        $this->em = $em;
        $this->multidomainEntityClassFinder = $multidomainEntityClassFinder;
        $this->multiDomainEntityClassProvider = $multiDomainEntityClassProvider;
        $this->entityNotNullableColumnsFinder = $entityNotNullableColumnsFinder;
    }

    /**
     * @return string[]
     */
    public function getMultidomainEntitiesNames()
    {
        return $this->multidomainEntityClassFinder->getMultidomainEntitiesNames(
            $this->em->getMetadataFactory()->getAllMetadata(),
            $this->multiDomainEntityClassProvider->getIgnoredMultidomainEntitiesNames(),
            $this->multiDomainEntityClassProvider->getManualMultidomainEntitiesNames()
        );
    }

    /**
     * @return string[][]
     */
    public function getAllNotNullableColumnNamesIndexedByTableName()
    {
        $multidomainClassesMetadata = [];
        foreach ($this->getMultidomainEntitiesNames() as $multidomainEntityName) {
            $multidomainClassesMetadata[] = $this->em->getMetadataFactory()->getMetadataFor($multidomainEntityName);
        }

        return $this->entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName($multidomainClassesMetadata);
    }
}
