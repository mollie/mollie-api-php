<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\SessionStatus;
use Mollie\Api\Utils\Utility;

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

    /**
     * @deprecated Not part of the Checkout Sessions API response.
     */
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
     * Customer details Mollie collects during checkout (private beta).
     *
     * @var array<string>|null
     */
    public ?array $requiredCustomerDetails = null;

    public ?string $profileId = null;

    public ?string $createdAt = null;

    public ?string $expiredAt = null;

    public ?string $completedAt = null;

    /**
     * @var \stdClass
     */
    public $_links;

    public function isOpen(): bool
    {
        return Utility::equals($this->status, SessionStatus::Open);
    }

    public function isExpired(): bool
    {
        return Utility::equals($this->status, SessionStatus::Expired);
    }

    public function isCompleted(): bool
    {
        return Utility::equals($this->status, SessionStatus::Completed);
    }

    /**
     * @deprecated The Checkout Sessions API only returns a `self` link, so this always returns null.
     *             Use the clientAccessToken with Mollie's client-side components instead.
     */
    public function getRedirectUrl(): ?string
    {
        if (empty($this->_links->redirect)) {
            return null;
        }

        return $this->_links->redirect->href;
    }
}
