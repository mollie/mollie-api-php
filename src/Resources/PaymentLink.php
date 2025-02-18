<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Traits\HasMode;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class PaymentLink extends BaseResource
{
    use HasMode;

    /**
     * Id of the payment link (on the Mollie platform).
     *
     * @var string
     */
    public $id;

    /**
     * Mode of the payment link, either "live" or "test" depending on the API Key that was
     * used.
     *
     * @var string
     */
    public $mode;

    /**
     * The profile ID this payment link belongs to.
     *
     * @example pfl_QkEhN94Ba
     *
     * @var string
     */
    public $profileId;

    /**
     * UTC datetime the payment link was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $createdAt;

    /**
     * UTC datetime the payment was paid in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $paidAt;

    /**
     * Whether the payment link is archived. Customers will not be able to complete
     * payments on archived payment links.
     *
     * @var bool
     */
    public $archived;

    /**
     * UTC datetime the payment link was updated in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $updatedAt;

    /**
     * UTC datetime - the expiry date of the payment link in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $expiresAt;

    /**
     * Amount object containing the value and currency
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * Description of the payment link that is shown to the customer during the payment,
     * and possibly on the bank or credit card statement.
     *
     * @var string
     */
    public $description;

    /**
     * Redirect URL set on this payment
     *
     * @var string
     */
    public $redirectUrl;

    /**
     * Webhook URL set on this payment link
     *
     * @var string|null
     */
    public $webhookUrl;

    /**
     * @var \stdClass
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
