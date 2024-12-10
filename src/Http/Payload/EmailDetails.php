<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\DataProvider;
use Mollie\Api\Traits\ComposableFromArray;

class EmailDetails implements DataProvider
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

    public function data()
    {
        return [
            'subject' => $this->subject,
            'body' => $this->body,
        ];
    }
}
