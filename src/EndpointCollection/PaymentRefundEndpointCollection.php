<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Factories\GetPaginatedPaymentRefundQueryFactory;
use Mollie\Api\Http\Query\GetPaginatedPaymentRefundQuery;
use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Resources\RefundCollection;

class PaymentRefundEndpointCollection extends EndpointCollection
{
    /**
     * @param  array|GetPaginatedPaymentRefundQuery  $query
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function page(string $paymentId, $query = []): RefundCollection
    {
        if (! $query instanceof GetPaginatedPaymentRefundQuery) {
            $query = GetPaginatedPaymentRefundQueryFactory::new($query)
                ->create();
        }

        return $this->send(new GetPaginatedPaymentRefundsRequest(
            $paymentId,
            $query
        ));
    }
}
