<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Factories\UpdateWebhookRequestFactory;
use Mollie\Api\Http\Requests\DeleteWebhookRequest;
use Mollie\Api\Http\Requests\TestWebhookRequest;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\WebhookStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Webhook extends BaseResource
{
    use HasMode;

    /**
     * Indicates the response contains a webhook subscription object.
     * Will always contain the string "webhook" for this endpoint.
     *
     * @var string
     */
    public $resource;

    /**
     * The identifier uniquely referring to this subscription.
     *
     * @var string
     */
    public $id;

    /**
     * The subscription's events destination.
     *
     * @var string
     */
    public $url;

    /**
     * The identifier uniquely referring to the profile that created the subscription.
     *
     * @var string
     */
    public $profileId;

    /**
     * The subscription's date time of creation.
     *
     * @var string
     */
    public $createdAt;

    /**
     * The subscription's name.
     *
     * @var string
     */
    public $name;

    /**
     * The events types that are subscribed.
     *
     * @var array<string>
     */
    public $eventTypes;

    /**
     * The subscription's current status.
     * Possible values: enabled, blocked, disabled
     *
     * @var string
     */
    public $status;

    /**
     * The webhook's secret.
     *
     * Only available once after creation.
     *
     * @var string|null
     */
    public $webhookSecret;

    /**
     * The webhook's mode.
     *
     * @var string
     */
    public $mode;

    /**
     * @var \stdClass
     */
    public $_links;

    public function enabled(): bool
    {
        return $this->status === WebhookStatus::ENABLED;
    }

    public function disabled(): bool
    {
        return $this->status === WebhookStatus::DISABLED;
    }

    public function blocked(): bool
    {
        return $this->status === WebhookStatus::BLOCKED;
    }

    public function deleted(): bool
    {
        return $this->status === WebhookStatus::DELETED;
    }

    /**
     * Update this webhook.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(array $data = []): Webhook
    {
        $request = UpdateWebhookRequestFactory::new($this->id)
            ->withPayload($data)
            ->create();

        /** @var Webhook */
        return $this->connector->send($request->test($this->isInTestmode()));
    }

    /**
     * Delete this webhook.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function delete(): void
    {
        $this->connector->send((new DeleteWebhookRequest($this->id))->test($this->isInTestmode()));
    }

    /**
     * Test this webhook by sending a test event.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function test(): AnyResource
    {
        /** @var AnyResource */
        return $this->connector->send((new TestWebhookRequest($this->id))->test($this->isInTestmode()));
    }
}
