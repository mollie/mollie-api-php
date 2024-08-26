<?php

namespace Mollie\Api\Http\EndpointCollection;

use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Resources\RefundCollection;

class PaymentRefundEndpointCollection extends Endpoint
{
    /**
     * @param string $paymentId
     * @param array $filters
     *
     * @return RefundCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function page(string $paymentId, array $filters = []): RefundCollection
    {
        return $this->send(new GetPaginatedPaymentRefundsRequest(
            $paymentId,
            $filters
        ));
    }
}
