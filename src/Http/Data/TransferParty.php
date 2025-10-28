<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class TransferParty implements Arrayable
{
    use ComposableFromArray;

    private string $type;

    private string $id;

    private string $description;

    public function __construct(
        string $id,
        string $description,
        string $type = 'organization'
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->type = $type;
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
