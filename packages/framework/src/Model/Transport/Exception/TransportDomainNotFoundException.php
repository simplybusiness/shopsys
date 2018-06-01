<?php

namespace Shopsys\FrameworkBundle\Model\Transport\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransportDomainNotFoundException extends NotFoundHttpException implements TransportException
{
    /**
     * @param int $transportId
     * @param int $domainId
     * @param \Exception|null $previous
     */
    public function __construct($transportId, $domainId, \Exception $previous = null)
    {
        $message = sprintf(
            'TransportDomain for transport ID %d and domain ID %d not found.',
            $transportId,
            $domainId
        );
        parent::__construct($message, $previous, 0);
    }
}
