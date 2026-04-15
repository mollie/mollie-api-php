<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class PaymentLink extends BaseResource
{
    use HasMode;

    public string $id;

    /**
     * Mode of the payment link, either "live" or "test" depending on the API Key that was used.
     */
    public string $mode;

    /**
     * The profile ID this payment link belongs to.
     */
    public string $profileId;

    public ?string $createdAt = null;

    public ?string $paidAt = null;

    public bool $archived = false;

    public ?string $updatedAt = null;

    public ?string $expiresAt = null;

    /**
     * Amount object containing the value and currency.
     */
    public ?Money $amount = null;

    /**
     * The minimum amount. Only used for payment links without a fixed amount.
     */
    public ?Money $minimumAmount = null;

    /**
     * The order lines for this payment link (used for Klarna and other BNPL methods).
     *
     * @var array|null
     */
    public ?array $lines = null;

    public ?Address $billingAddress = null;

    public ?Address $shippingAddress = null;

    public string $description;

    public ?string $redirectUrl = null;

    public ?string $webhookUrl = null;

    /**
     * @var \stdClass|null
     */
    public $_links;

    /**
     * Is this payment paid for?
     */
    public function isPaid(): bool
    {
        return ! empty($this->paidAt);
    }

    /**
     * Get the checkout URL where the customer can complete the payment.
     */
    public function getCheckoutUrl(): ?string
    {
        if (empty($this->_links->paymentLink)) {
            return null;
        }

        return $this->_links->paymentLink->href;
    }

    /**
     * Persist the current local Payment Link state to the Mollie API.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update(): ?PaymentLink
    {
        return $this->connector->paymentLinks->update($this->id, $this->withMode([
            'description' => $this->description,
            'archived' => $this->archived,
        ]));
    }

    /**
     * Archive this Payment Link.
     *
     * @return \Mollie\Api\Resources\PaymentLink
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function archive()
    {
        return $this->connector->paymentLinks->update($this->id, $this->withMode([
            'archived' => true,
        ]));
    }

    /**
     * Retrieve a paginated list of payments associated with this payment link.
     *
     * @return mixed|\Mollie\Api\Resources\BaseCollection
     */
    public function payments(?string $from = null, ?int $limit = null, array $filters = [])
    {
        return $this->connector->paymentLinkPayments->pageFor(
            $this,
            $from,
            $limit,
            $this->withMode($filters)
        );
    }
}
