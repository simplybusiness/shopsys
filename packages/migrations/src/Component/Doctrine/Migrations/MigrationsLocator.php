<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class MigrationsLocator
{
    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    private $kernel;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     */
    public function __construct(KernelInterface $kernel, Filesystem $filesystem)
    {
        $this->kernel = $kernel;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $relativeDirectory
     * @param string $relativeNamespace
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation[]
     */
    public function getMigrationsLocations(string $relativeDirectory, string $relativeNamespace)
    {
        $migrationsLocations = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            $migrationsLocation = $this->createMigrationsLocation($bundle, $relativeDirectory, $relativeNamespace);
            if ($this->filesystem->exists($migrationsLocation->getDirectory())) {
                $migrationsLocations[] = $migrationsLocation;
            }
        }

        return $migrationsLocations;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Bundle\BundleInterface $bundle
     * @param string $relativeDirectory
     * @param string $relativeNamespace
     * @return \Shopsys\MigrationBundle\Component\Doctrine\Migrations\MigrationsLocation
     */
    public function createMigrationsLocation(BundleInterface $bundle, string $relativeDirectory, string $relativeNamespace)
    {
        return new MigrationsLocation(
            $bundle->getPath() . '/' . $relativeDirectory,
            $bundle->getNamespace() . '\\' . $relativeNamespace
        );
    }
}
