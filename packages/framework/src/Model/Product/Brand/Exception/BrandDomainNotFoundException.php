<?php

namespace Shopsys\FrameworkBundle\Model\Product\Brand\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class BrandDomainNotFoundException extends HttpException implements BrandException
{
    public function __construct($brandId, $domainId, \Exception $previous = null)
    {
        $message = sprintf(
            'BrandDomain for brand ID %d and domain ID %d not found.',
            $brandId,
            $domainId
        );
        parent::__construct(500, $message, $previous);
    }
}
