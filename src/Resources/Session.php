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

    public string $clientAccessToken;

    public string $redirectUrl;

    public ?string $cancelUrl = null;

    public Money $amount;

    public string $description;

    public ?Address $shippingAddress = null;

    public ?Address $billingAddress = null;

    public ?string $customerId = null;

    public ?string $sequenceType = null;

    /**
     * @var object|array|null
     */
    public $metadata = null;

    /**
     * @var \stdClass|null
     */
    public $payment = null;

    /**
     * @var array|object[]|null
     */
    public ?array $lines = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isOpen(): bool
    {
        return $this->status === SessionStatus::Open;
    }

    public function isExpired(): bool
    {
        return $this->status === SessionStatus::Expired;
    }

    public function isCompleted(): bool
    {
        return $this->status === SessionStatus::Completed;
    }

    public function getRedirectUrl(): ?string
    {
        if (empty($this->_links->redirect)) {
            return null;
        }

        return $this->_links->redirect->href;
    }
}
