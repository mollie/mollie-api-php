<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;

class CreatePaymentRefundRequestFactory extends RequestFactory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function create(): CreatePaymentRefundRequest
    {
        return new CreatePaymentRefundRequest(
            $this->paymentId,
            $this->payload('description'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->transformFromPayload('metadata', Metadata::class),
            $this->payload('reverseRouting'),
            $this
                ->transformFromPayload(
                    'routingReversals',
                    fn ($items) => RefundRouteCollectionFactory::new($items)->create()
                ),
        );
    }
}
