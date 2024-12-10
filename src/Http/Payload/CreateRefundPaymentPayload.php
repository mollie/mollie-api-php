<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Traits\Makeable;

class CreateRefundPaymentPayload extends DataBag
{
    use Makeable;

    public string $description;

    public Money $amount;

    public ?Metadata $metadata = null;

    public ?bool $reverseRouting = null;

    /**
     * @var DataCollection<RefundRoute>
     */
    public ?DataCollection $routingReversals = null;

    public function __construct(
        string $description,
        Money $amount,
        ?Metadata $metadata = null,
        ?bool $reverseRouting = null,
        ?DataCollection $routingReversals = null
    ) {
        $this->description = $description;
        $this->amount = $amount;
        $this->metadata = $metadata;
        $this->reverseRouting = $reverseRouting;
        $this->routingReversals = $routingReversals;
    }

    public function toArray(): array
    {
        return [
            'description' => $this->description,
            'amount' => $this->amount,
            'metadata' => $this->metadata,
            'reverseRouting' => $this->reverseRouting,
            'routingReversals' => $this->routingReversals,
        ];
    }
}
