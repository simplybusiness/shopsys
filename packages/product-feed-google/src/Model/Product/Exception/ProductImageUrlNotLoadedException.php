<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product\Exception;

use Exception;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductImageUrlNotLoadedException extends Exception implements ProductException
{
    public function __construct(DomainConfig $domainConfig, Product $product, Exception $previous = null)
    {
        $message = sprintf(
            'URL for image of product with ID %d on %s have not been loaded via ProductsUrlProvider::loadForProducts().',
            $product->getId(),
            $domainConfig->getName()
        );
        parent::__construct($message, 0, $previous);
    }
}
