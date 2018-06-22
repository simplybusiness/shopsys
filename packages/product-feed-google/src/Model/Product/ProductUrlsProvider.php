<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\Exception\ProductImageUrlNotLoadedException;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\Exception\ProductUrlNotLoadedException;

class ProductUrlsProvider
{
    /**
     * @var string[]
     */
    protected $loadedProductUrls = [];

    /**
     * @var string[]|null[]
     */
    protected $loadedProductImageUrls = [];

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Collection\ProductCollectionFacade
     */
    private $productCollectionFacade;

    public function __construct(ProductCollectionFacade $productCollectionFacade)
    {
        $this->productCollectionFacade = $productCollectionFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $products
     */
    public function loadForProducts(DomainConfig $domainConfig, array $products): void
    {
        $productUrlsById = $this->productCollectionFacade->getAbsoluteUrlsIndexedByProductId($products, $domainConfig);
        $productImageUrlsById = $this->productCollectionFacade->getImagesUrlsIndexedByProductId($products, $domainConfig);

        foreach ($products as $product) {
            $key = $this->getKey($domainConfig, $product);
            $productId = $product->getId();

            $this->loadedProductUrls[$key] = $productUrlsById[$productId];
            $this->loadedProductImageUrls[$key] = $productImageUrlsById[$productId];
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string
     */
    public function getProductUrl(DomainConfig $domainConfig, Product $product): string
    {
        $key = $this->getKey($domainConfig, $product);
        if (!array_key_exists($key, $this->loadedProductUrls)) {
            throw new ProductUrlNotLoadedException($domainConfig, $product);
        }

        return $this->loadedProductUrls[$key];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string|null
     */
    public function getProductImageUrl(DomainConfig $domainConfig, Product $product): ?string
    {
        $key = $this->getKey($domainConfig, $product);
        if (!array_key_exists($key, $this->loadedProductImageUrls)) {
            throw new ProductImageUrlNotLoadedException($domainConfig, $product);
        }

        return $this->loadedProductImageUrls[$key];
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return string
     */
    protected function getKey(DomainConfig $domainConfig, Product $product): string
    {
        return $domainConfig->getId() . '-' . $product->getId();
    }
}
