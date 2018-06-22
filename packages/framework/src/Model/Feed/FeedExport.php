<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Symfony\Component\Filesystem\Filesystem;

class FeedExport
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedInterface
     */
    protected $feed;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    protected $domainConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedRenderer
     */
    protected $feedRenderer;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $feedFilepath;

    /**
     * @var int|null
     */
    protected $lastSeekId;

    /**
     * @var resource|null
     */
    protected $openedFile;

    /**
     * @var bool
     */
    protected $finished = false;

    public function __construct(
        FeedInterface $feed,
        DomainConfig $domainConfig,
        FeedRenderer $feedRenderer,
        Filesystem $filesystem,
        EntityManagerInterface $em,
        string $feedFilepath,
        ?int $lastSeekId
    ) {
        $this->feed = $feed;
        $this->domainConfig = $domainConfig;
        $this->feedRenderer = $feedRenderer;
        $this->filesystem = $filesystem;
        $this->em = $em;
        $this->feedFilepath = $feedFilepath;
        $this->lastSeekId = $lastSeekId;
    }

    public function generateBatch(): void
    {
        if ($this->finished) {
            return;
        }

        $itemsInBatch = $this->feed->getItems($this->domainConfig, $this->lastSeekId, self::BATCH_SIZE);

        if ($this->lastSeekId === null) {
            $this->writeBegin();
        }

        $countInBatch = 0;
        foreach ($itemsInBatch as $item) {
            $this->writeItem($item);
            $this->lastSeekId = $item->getSeekId();
            $countInBatch++;
        }

        if ($countInBatch < self::BATCH_SIZE) {
            $this->writeEnd();
            $this->finishFile();
        } else {
            $this->closeFile();
        }

        $this->em->clear();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface
     */
    public function getFeedInfo(): FeedInfoInterface
    {
        return $this->feed->getInfo();
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getDomainConfig(): DomainConfig
    {
        return $this->domainConfig;
    }

    /**
     * @return int|null
     */
    public function getLastSeekId(): ?int
    {
        return $this->lastSeekId;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    protected function writeBegin(): void
    {
        $this->openedFile = fopen($this->getTemporaryFilepath(), 'w');

        $content = $this->feedRenderer->renderBegin($this->domainConfig);

        fwrite($this->openedFile, $content);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedItemInterface $item
     */
    protected function writeItem(FeedItemInterface $item): void
    {
        if ($this->openedFile === null) {
            $this->openedFile = fopen($this->getTemporaryFilepath(), 'a');
        }

        $content = $this->feedRenderer->renderItem($this->domainConfig, $item);

        fwrite($this->openedFile, $content);
    }

    protected function writeEnd(): void
    {
        if ($this->openedFile === null) {
            $this->openedFile = fopen($this->getTemporaryFilepath(), 'a');
        }

        $content = $this->feedRenderer->renderEnd($this->domainConfig);

        fwrite($this->openedFile, $content);
    }

    protected function closeFile(): void
    {
        if ($this->openedFile !== null) {
            fclose($this->openedFile);

            $this->openedFile = null;
        }
    }

    protected function finishFile(): void
    {
        $this->closeFile();
        $this->filesystem->rename($this->getTemporaryFilepath(), $this->feedFilepath, true);
        $this->finished = true;
    }

    /**
     * @return string
     */
    protected function getTemporaryFilepath(): string
    {
        return $this->feedFilepath . self::TEMPORARY_FILENAME_SUFFIX;
    }
}
