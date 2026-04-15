<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class PaymentDetails implements Arrayable
{
    use ComposableFromArray;

    public ?string $sourceReference;

    public function __construct(
        public string $source,
        /** @deprecated use $sourceReference instead */
        ?string $sourceDescription = null,
        ?string $sourceReference = null,
    ) {
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
