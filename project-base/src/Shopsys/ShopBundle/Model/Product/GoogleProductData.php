<?php

namespace Shopsys\ShopBundle\Model\Product;

use Shopsys\ProductFeed\GoogleBundle\Model\Product\GoogleProductDomainData as  BaseGoogleProductData;

class GoogleProductData extends BaseGoogleProductData
{
    /**
     * @var int
     */
    public $priority;

    public function __construct()
    {
        parent::__construct();
        $this->priority = 0;
    }
}
