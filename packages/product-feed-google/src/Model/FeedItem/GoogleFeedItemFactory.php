<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\FeedItem;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\ProductUrlsProvider;

class GoogleFeedItemFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    protected $productPriceCalculationForUser;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade
     */
    protected $currencyFacade;

    /**
     * @var \Shopsys\ProductFeed\GoogleBundle\Model\Product\ProductUrlsProvider
     */
    protected $productUrlsProvider;

    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculationForUser,
        CurrencyFacade $currencyFacade,
        ProductUrlsProvider $productUrlsProvider
    ) {
        $this->productPriceCalculationForUser = $productPriceCalculationForUser;
        $this->currencyFacade = $currencyFacade;
        $this->productUrlsProvider = $productUrlsProvider;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\ProductFeed\GoogleBundle\Model\FeedItem\GoogleFeedItem
     */
    public function create(DomainConfig $domainConfig, Product $product): GoogleFeedItem
    {
        return new GoogleFeedItem(
            $product->getId(),
            $product->getName($domainConfig->getLocale()),
            $this->getBrandName($product),
            $product->getDescription($domainConfig->getId()),
            $product->getEan(),
            $product->getPartno(),
            $this->productUrlsProvider->getProductUrl($domainConfig, $product),
            $this->productUrlsProvider->getProductImageUrl($domainConfig, $product),
            $product->isSellingDenied(),
            $this->getPrice($domainConfig, $product),
            $this->getCurrency($domainConfig)
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    protected function getBrandName(Product $product): ?string
    {
        $brand = $product->getBrand();

        return $brand !== null ? $brand->getName() : null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    protected function getPrice(DomainConfig $domainConfig, Product $product): Price
    {
        return $this->productPriceCalculationForUser->calculatePriceForUserAndDomainId(
            $product,
            $domainConfig->getId(),
            null
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    protected function getCurrency(DomainConfig $domainConfig): Currency
    {
        return $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainConfig->getId());
    }
}
