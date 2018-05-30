<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface;

class PrototypeFinder implements MigrationFinderInterface
{
    /**
     * @var \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocator
     */
    private $locator;

    /**
     * @var \Doctrine\DBAL\Migrations\Finder\MigrationFinderInterface
     */
    private $finder;

    public function __construct(MigrationsLocator $locator, MigrationFinderInterface $finder)
    {
        $this->locator = $locator;
        $this->finder = $finder;
    }

    /**
     * Find all the migrations in all registered bundles for the given path and namespace postfix
     *
     * eg. findMigrations('Migrations', 'Migrations') looks for migrations in directory Migrations in roots of all bundles
     *
     * @param   string $directory The directory within a bundle in which to look for migrations
     * @param   string|null $namespace The namespace within a bundle of the classes to load
     * @return  string[] An array of class names that were found with the version as keys.
     */
    public function findMigrations($directory, $namespace = null)
    {
        if ($namespace === null) {
            $namespace = $directory;
        }

        $migrations = [];

        foreach ($this->locator->getMigrationsLocations($directory, $namespace) as $location) {
            $migrations += $this->finder->findMigrations($location->getDirectory(), $location->getNamespace());
        }

        // Let's install the migrations in some defined order (here it's by version number)
        ksort($migrations);

        return $migrations;
    }
}
