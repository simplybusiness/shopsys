<?php

namespace Shopsys\FrameworkBundle\Command;

use League\Flysystem\FilesystemInterface;
use Shopsys\FrameworkBundle\Component\Image\DirectoryStructureCreator as ImageDirectoryStructureCreator;
use Shopsys\FrameworkBundle\Component\UploadedFile\DirectoryStructureCreator as UploadedFileDirectoryStructureCreator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateApplicationDirectoriesCommand extends Command
{

    /**
     * @var string
     */
    protected static $defaultName = 'shopsys:create-directories';

    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\DirectoryStructureCreator
     */
    private $imageDirectoryStructureCreator;

    /**
     * @var \Shopsys\FrameworkBundle\Component\UploadedFile\DirectoryStructureCreator
     */
    private $uploadedFileDirectoryStructureCreator;

    /**
     * @param string $rootDirectory
     * @param string $webDirectory
     * @param \League\Flysystem\FilesystemInterface $filesystem
     * @param \Shopsys\FrameworkBundle\Component\Image\DirectoryStructureCreator $imageDirectoryStructureCreator
     * @param \Shopsys\FrameworkBundle\Component\UploadedFile\DirectoryStructureCreator $uploadedFileDirectoryStructureCreator
     */
    public function __construct(
        FilesystemInterface $filesystem,
        ImageDirectoryStructureCreator $imageDirectoryStructureCreator,
        UploadedFileDirectoryStructureCreator $uploadedFileDirectoryStructureCreator
    ) {
        $this->filesystem = $filesystem;
        $this->imageDirectoryStructureCreator = $imageDirectoryStructureCreator;
        $this->uploadedFileDirectoryStructureCreator = $uploadedFileDirectoryStructureCreator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Create application directories for locks, docs, content, images, uploaded files, etc.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->createMiscellaneousDirectories($output);
        $this->createImageDirectories($output);
        $this->createUploadedFileDirectories($output);
    }

    private function createMiscellaneousDirectories(OutputInterface $output)
    {
        $directories = [
            '/build/stats',
            '/docs/generated',
            '/var/cache',
            '/var/lock',
            '/var/logs',
            '/var/errorPages',
            '/web/assets/admin/styles',
            '/web/assets/frontend/styles',
            '/web/assets/scripts',
            '/web/content/feeds',
            '/web/content/sitemaps',
            '/web/content/wysiwyg',
        ];

        foreach ($directories as $directory) {
            $this->filesystem->createDir($directory);
        }

        $output->writeln('<fg=green>Miscellaneous application directories were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createImageDirectories(OutputInterface $output)
    {
        $this->imageDirectoryStructureCreator->makeImageDirectories();

        $output->writeln('<fg=green>Directories for images were successfully created.</fg=green>');
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    private function createUploadedFileDirectories(OutputInterface $output)
    {
        $this->uploadedFileDirectoryStructureCreator->makeUploadedFileDirectories();

        $output->writeln('<fg=green>Directories for UploadedFile entities were successfully created.</fg=green>');
    }
}
