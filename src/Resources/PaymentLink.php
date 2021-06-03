<?php

namespace Mollie\Api\Resources;

class PaymentLink extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

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
     * UTC datetime the payment link was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $createdAt;

    /**
     * UTC datetime the payment was paid in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $paidAt;

    /**
     * UTC datetime the payment link was updated in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $updateddAt;

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
     *
     * @return bool
     */
    public function isPaid()
    {
        return ! empty($this->paidAt);
    }

    /**
     * Get the checkout URL where the customer can complete the payment.
     *
     * @return string|null
     */
    public function getPaymentLinkUrl()
    {
        if (empty($this->_links->paymentLink)) {
            return null;
        }

        return $this->_links->paymentLink->href;
    }
}
