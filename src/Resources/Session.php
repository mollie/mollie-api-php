<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Traits\HasMode;
use Mollie\Api\Types\SessionStatus;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Session extends BaseResource
{
    use HasMode;

    /**
     * The session's unique identifier,
     *
     * @example sess_dfsklg13jO
     *
     * @var string
     */
    public $id;

    /**
     * Status of the session.
     *
     * @var string
     */
    public $status;

    /**
     * Client access token for the session.
     *
     * @var string
     */
    public $clientAccessToken;

    /**
     * The URL the buyer will be redirected to in case the
     * payment preparation process requires a 3rd party redirect.
     *
     * @var string
     */
    public $redirectUrl;

    /**
     * The URL the buyer will be redirected to if they
     * cancel their payment during a 3rd party redirect..
     *
     * @var string
     */
    public $cancelUrl;

    /**
     * The amount you intend to charge containing the value and currency.
     *
     * Note - this is not necessarily the final amount of the
     * payment.You will specify the final amount upon Order creation
     *
     * @var \stdClass
     */
    public $amount;

    /**
     * Description of the payment intent.
     *
     * @var string
     */
    public $description;

    /**
     * The person and the address the payment is shipped to.
     *
     * @var \stdClass|null
     */
    public $shippingAddress;

    /**
     * The person and the address the payment is billed to.
     *
     * @var \stdClass|null
     */
    public $billingAddress;

    /**
     * ID of the customer the session is created for.
     *
     * @var string|null
     */
    public $customerId;

    /**
     * Sequence type for recurring payments.
     *
     * @var string|null
     */
    public $sequenceType;

    /**
     * Metadata associated with the session.
     *
     * @var object|array|null
     */
    public $metadata;

    /**
     * Payment settings for the session.
     *
     * @var \stdClass|null
     */
    public $payment;

    /**
     * Order lines for the session.
     *
     * @var array|object[]|null
     */
    public $lines;

    /**
     * An object with several URL objects relevant to the customer. Every URL object will contain an href and a type field.
     *
     * @var \stdClass
     */
    public $_links;

    public function isOpen()
    {
        return $this->status === SessionStatus::OPEN;
    }

    public function isExpired()
    {
        return $this->status === SessionStatus::EXPIRED;
    }

    public function isCompleted()
    {
        return $this->status === SessionStatus::COMPLETED;
    }

    /**
     * Saves the session's updatable properties.
     *
     * @return \Mollie\Api\Resources\Session
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update()
    {
        $body = [
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ];

        return $this->connector->sessions->update($this->id, $this->withMode($body));
    }

    /**
     * Cancels this session.
     *
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel(): void
    {
        $this->connector->sessions->cancel($this->id);
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        if (empty($this->_links->redirect)) {
            return null;
        }

        return $this->_links->redirect->href;
    }
}
