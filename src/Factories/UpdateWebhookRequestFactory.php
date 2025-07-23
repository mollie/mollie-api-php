<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\UpdateWebhookRequest;

class UpdateWebhookRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): UpdateWebhookRequest
    {
        return new UpdateWebhookRequest(
            $this->id,
            $this->payload('url'),
            $this->payload('name'),
            $this->payload('eventTypes')
        );
    }
}
