<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class PaymentDetails implements Arrayable
{
    use ComposableFromArray;

    public string $source;

    public ?string $sourceDescription;

    public function __construct(
        string $source,
        ?string $sourceDescription = null
    ) {
        $this->source = $source;
        $this->sourceDescription = $sourceDescription;
    }

    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'sourceDescription' => $this->sourceDescription,
        ];
    }
}
