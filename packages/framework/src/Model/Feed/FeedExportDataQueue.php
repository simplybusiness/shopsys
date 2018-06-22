<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class FeedExportDataQueue
{
    /**
     * @var array
     */
    private $dataInQueue = [];

    /**
     * @param string[] $feedNames
     * @param DomainConfig[] $domains
     */
    public function __construct(array $feedNames, array $domains)
    {
        foreach ($feedNames as $feedName) {
            foreach ($domains as $domain) {
                $this->dataInQueue[] = ['feed_name' => $feedName, 'domain' => $domain];
            }
        }
    }

    /**
     * @return string
     */
    public function getCurrentFeedName(): string
    {
        return current($this->dataInQueue)['feed_name'];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig
     */
    public function getCurrentDomain(): DomainConfig
    {
        return current($this->dataInQueue)['domain'];
    }

    /**
     * @return bool
     */
    public function next(): bool
    {
        array_shift($this->dataInQueue);

        return !$this->isEmpty();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->dataInQueue) === 0;
    }
}
