<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\SessionStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Session extends BaseResource
{
    use HasMode;

    public string $id;

    public SessionStatus|string $status;

    public ?string $failedAt = null;

    public string $authenticationId;

    public string $nextAction;

    public string $redirectUrl;

    public string $cancelUrl;

    public Money $amount;

    public string $description;

    public string $method;

    /**
     * @var \stdClass
     */
    public $methodDetails;

    /**
     * @deprecated
     */
    public ?Address $shippingAddress = null;

    /**
     * @deprecated
     */
    public ?Address $billingAddress = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isCreated(): bool
    {
        return $this->status === SessionStatus::Created;
    }

    public function isReadyForProcessing(): bool
    {
        return $this->status === SessionStatus::ReadyForProcessing;
    }

    public function isCompleted(): bool
    {
        return $this->status === SessionStatus::Completed;
    }

    public function hasFailed(): bool
    {
        return $this->status === SessionStatus::Failed;
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): Session
    {
        $body = [
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ];

        return $this->connector->sessions->update($this->id, $this->withMode($body));
    }

    /**
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(): void
    {
        $this->connector->sessions->cancel($this->id);
    }

    public function getRedirectUrl(): ?string
    {
        if (empty($this->_links->redirect)) {
            return null;
        }

        return $this->_links->redirect->href;
    }
}
