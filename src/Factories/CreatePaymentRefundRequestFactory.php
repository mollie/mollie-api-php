<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\Metadata;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;

class CreatePaymentRefundRequestFactory extends Factory
{
    private string $paymentId;

    public function __construct(string $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public static function new(string $paymentId): self
    {
        return new self($paymentId);
    }

    public function create(): CreatePaymentRefundRequest
    {
        return new CreatePaymentRefundRequest(
            $this->paymentId,
            $this->payload('description'),
            MoneyFactory::new($this->payload('amount'))->create(),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->payload('reverseRouting'),
            $this
                ->mapIfNotNull(
                    'routingReversals',
                    fn (array $items) => RefundRouteCollectionFactory::new($items)->create()
                ),
        );
    }
}
