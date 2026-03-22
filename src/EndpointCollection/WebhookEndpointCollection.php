<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\RequestException;
use Mollie\Api\Factories\CreateWebhookRequestFactory;
use Mollie\Api\Factories\UpdateWebhookRequestFactory;
use Mollie\Api\Http\Requests\DeleteWebhookRequest;
use Mollie\Api\Http\Requests\GetPaginatedWebhooksRequest;
use Mollie\Api\Http\Requests\GetWebhookRequest;
use Mollie\Api\Http\Requests\TestWebhookRequest;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\Webhook;
use Mollie\Api\Resources\WebhookCollection;

class WebhookEndpointCollection extends EndpointCollection
{
    /**
     * Creates a webhook in Mollie.
     *
     * @param  array  $payload  An array containing details on the webhook.
     *
     * @throws RequestException
     */
    public function create(array $payload = [], bool $test = false): Webhook
    {
        $request = CreateWebhookRequestFactory::new()
            ->withPayload($payload)
            ->create();

        /** @var Webhook */
        return $this->send($request->test($test));
    }

    /**
     * Retrieve a webhook from Mollie.
     *
     * Will throw an ApiException if the webhook id is invalid or the resource cannot be found.
     *
     * @throws RequestException
     */
    public function get(string $id, bool $test = false): Webhook
    {
        /** @var Webhook */
        return $this->send((new GetWebhookRequest($id))->test($test));
    }

    /**
     * Update a webhook.
     *
     * @throws RequestException
     */
    public function update(string $id, array $payload = [], bool $test = false): Webhook
    {
        $request = UpdateWebhookRequestFactory::new($id)
            ->withPayload($payload)
            ->create();

        /** @var Webhook */
        return $this->send($request->test($test));
    }

    /**
     * Delete a webhook from Mollie.
     *
     * @throws RequestException
     */
    public function delete(string $id, bool $test = false): void
    {
        $this->send((new DeleteWebhookRequest($id))->test($test));
    }

    /**
     * Test a webhook by sending a test event.
     *
     * @throws RequestException
     */
    public function test(string $id, bool $test = false): AnyResource
    {
        /** @var AnyResource */
        return $this->send((new TestWebhookRequest($id))->test($test));
    }

    /**
     * Retrieves a collection of webhooks from Mollie.
     *
     * @throws RequestException
     */
    public function page(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        ?string $eventTypes = null
    ): WebhookCollection {
        $request = new GetPaginatedWebhooksRequest(
            $from,
            $limit,
            $sort,
            $eventTypes
        );

        /** @var WebhookCollection */
        return $this->send($request);
    }

    /**
     * Create an iterator for iterating over webhooks retrieved from Mollie.
     *
     * @param  string|null  $from  The first webhook ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        ?string $sort = null,
        ?string $eventTypes = null,
        bool $iterateBackwards = false,
        bool $test = false
    ): LazyCollection {
        $request = new GetPaginatedWebhooksRequest(
            $from,
            $limit,
            $sort,
            $eventTypes
        );

        return $this->send(
            $request
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($test)
        );
    }
}
