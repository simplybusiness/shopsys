<?php

namespace Shopsys\FrameworkBundle\Model\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentDomainNotFoundException extends NotFoundHttpException implements PaymentException
{
    /**
     * @param int $paymentId
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct($paymentId, $domainId, \Exception $previous = null)
    {
        $message = sprintf(
            'PaymentDomain for category ID %d and domain ID %d not found.',
            $paymentId,
            $domainId
        );
        parent::__construct($message, $previous, 0);
    }
}
