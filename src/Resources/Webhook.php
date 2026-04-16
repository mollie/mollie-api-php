<?php

declare(strict_types=1);

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


    public string $id;

    public string $url;

    public string $profileId;

    public string $createdAt;

    public string $name;

    /** @var array<string> */
    public array $eventTypes;

    public WebhookStatus|string $status;

    /** Only available once after creation. */
    public ?string $webhookSecret = null;

    public string $mode = 'live';

    /**
     * @var \stdClass
     */
    public $_links;

    public function enabled(): bool
    {
        return $this->status === WebhookStatus::Enabled;
    }

    public function disabled(): bool
    {
        return $this->status === WebhookStatus::Disabled;
    }

    public function blocked(): bool
    {
        return $this->status === WebhookStatus::Blocked;
    }

    public function deleted(): bool
    {
        return $this->status === WebhookStatus::Deleted;
    }

    /**
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
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function delete(): void
    {
        $this->connector->send((new DeleteWebhookRequest($this->id))->test($this->isInTestmode()));
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function test(): AnyResource
    {
        /** @var AnyResource */
        return $this->connector->send((new TestWebhookRequest($this->id))->test($this->isInTestmode()));
    }
}
