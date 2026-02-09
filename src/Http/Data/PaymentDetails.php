<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class PaymentDetails implements Arrayable
{
    use ComposableFromArray;

    public string $source;

    public ?string $sourceReference;

    public function __construct(
        string $source,
        /** @deprecated use $sourceReference instead */
        ?string $sourceDescription = null,
        ?string $sourceReference = null
    ) {
        $this->source = $source;
        $this->sourceReference = $sourceDescription ?? $sourceReference;
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'sourceReference' => $this->sourceReference,
        ];
    }
}
