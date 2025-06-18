<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Http\Requests\GetWebhookEventRequest;
use Mollie\Api\Resources\WebhookEvent;

class WebhookEventEndpointCollection extends EndpointCollection
{
    /**
     * Retrieve a webhook event from Mollie.
     *
     * Will throw an ApiException if the webhook event id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $id): WebhookEvent
    {
        /** @var WebhookEvent */
        return $this->send(new GetWebhookEventRequest($id));
    }
}
