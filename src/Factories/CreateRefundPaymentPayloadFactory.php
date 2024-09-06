<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Payload\CreateRefundPaymentPayload;
use Mollie\Api\Http\Payload\Metadata;

class CreateRefundPaymentPayloadFactory extends Factory
{
    public function create(): CreateRefundPaymentPayload
    {
        return new CreateRefundPaymentPayload(
            $this->get('description'),
            MoneyFactory::new($this->data['amount'])->create(),
            $this->mapIfNotNull('metadata', Metadata::class),
            $this->get('reverseRouting'),
            $this
                ->mapIfNotNull(
                    'routingReversals',
                    fn (array $items) => RefundRouteCollectionFactory::new($items)->create()
                ),
            $this->get('testmode')
        );
    }
}
