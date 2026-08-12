<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class TransferParty implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $id,
        public string $description,
        public string $type = 'organization',
    ) {
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'id' => $this->id,
            'description' => $this->description,
        ];
    }
}
