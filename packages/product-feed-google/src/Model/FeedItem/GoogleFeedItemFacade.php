<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductRepository;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\ProductUrlsProvider;

class GoogleFeedItemFacade
{
    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductRepository
     */
    protected $googleProductRepository;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItemFactory
     */
    protected $feedItemFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    protected $pricingGroupSettingFacade;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\Product\ProductUrlsProvider
     */
    protected $productUrlsProvider;

    public function __construct(
        GoogleProductRepository $googleProductRepository,
        GoogleFeedItemFactory $feedItemFactory,
        PricingGroupSettingFacade $pricingGroupSettingFacade,
        ProductUrlsProvider $productUrlsProvider
    ) {
        $this->googleProductRepository = $googleProductRepository;
        $this->feedItemFactory = $feedItemFactory;
        $this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
        $this->productUrlsProvider = $productUrlsProvider;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @param int $maxResults
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItem[]|iterable
     */
    public function getItems(DomainConfig $domainConfig, ?int $lastSeekId, int $maxResults): iterable
    {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());
        $products = $this->googleProductRepository->getProducts($domainConfig, $pricingGroup, $lastSeekId, $maxResults);
        $this->productUrlsProvider->loadForProducts($domainConfig, $products);

        foreach ($products as $product) {
            yield $this->feedItemFactory->create($domainConfig, $product);
        }
    }
}
