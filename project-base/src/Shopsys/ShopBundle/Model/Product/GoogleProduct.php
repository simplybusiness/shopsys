<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomain as BaseGoogleProduct;
use Doctrine\ORM\Mapping as ORM;
use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData;


/**
 * @ORM\Entity
 * @ORM\Table(name="google_product_domains")
 */
class GoogleProduct extends BaseGoogleProduct
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $priority;

    /**
     * @param \Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData $googleProductData
     */
    public function __construct(GoogleProductData $googleProductData)
    {
        parent::__construct($googleProductData);
        $this->priority = $googleProductData->priority;
    }

    /**
     * @param GoogleProductData $googleProductData
     */
    public function edit(GoogleProductDomainData $googleProductData)
    {
        parent::edit($googleProductData);
        $this->priority = $googleProductData->priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }
}