<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Traits\ComposableFromArray;

class EmailDetails implements Arrayable
{
    use ComposableFromArray;

    public string $subject;

    public string $body;

    public function __construct(
        string $subject,
        string $body
    ) {
        $this->subject = $subject;
        $this->body = $body;
    }

    public function toArray(): array
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }
}
