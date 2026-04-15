<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

readonly class EmailDetails implements Arrayable
{
    use ComposableFromArray;

    public function __construct(
        public string $subject,
        public string $body,
    ) {}

    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }
}
