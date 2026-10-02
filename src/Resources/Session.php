<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Types\SessionStatus;

class Session extends BaseResource
{
    use HasPresetOptions;

    /**
     * The session's unique identifier,
     *
     * @example sess_dfsklg13jO
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
     * The mode used to create this session.
     *
     * @var string
     */
    public $mode;

    /**
     * Client access token for rendering the checkout in your frontend.
     *
     * @var string
     */
    public $clientAccessToken;

    /**
     * UTC datetime indicating the time at which the Session failed in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var string|null
     */
    public $failedAt;

    /**
     * Unique identifier to record the Userʼs authentication with a method
     *
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var string
     */
    public $authenticationId;

    /**
     * Indicates the next action to take in the payment preparation flow.
     *
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var string
     */
    public $nextAction;

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
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var string|null
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
     * Payment method currently selected by the shopper.
     *
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var string
     */
    public $method;

    /**
     * All additional information relating to the selected method.
     *
     * @deprecated Not part of the Checkout Sessions API response.
     *
     * @var \stdClass
     */
    public $methodDetails;

    /**
     * The person and the address the payment is shipped to.
     *
     * @deprecated
     * @var \stdClass
     */
    public $shippingAddress;

    /**
     * The person and the address the payment is billed to.
     *
     * @deprecated
     * @var \stdClass
     *
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
     * Customer details Mollie collects during checkout.
     *
     * @var array<string>|null
     */
    public $requiredCustomerDetails;

    /**
     * The identifier referring to the profile this session belongs to.
     *
     * @example pfl_QkEhN94Ba
     *
     * @var string|null
     */
    public $profileId;

    /**
     * UTC datetime the session was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     *
     * @var string|null
     */
    public $createdAt;

    /**
     * UTC datetime the session expired in ISO-8601 format.
     *
     * @var string|null
     */
    public $expiredAt;

    /**
     * UTC datetime the session was completed in ISO-8601 format.
     *
     * @var string|null
     */
    public $completedAt;

    /**
     * An object with several URL objects relevant to the customer. Every URL object will contain an href and a type field.
     * @var \stdClass
     */
    public $_links;

    public function isOpen()
    {
        return $this->status === SessionStatus::STATUS_OPEN;
    }

    public function isCreated()
    {
        return $this->status === SessionStatus::STATUS_CREATED;
    }

    public function isReadyForProcessing()
    {
        return $this->status === SessionStatus::STATUS_READY_FOR_PROCESSING;
    }

    public function isCompleted()
    {
        return $this->status === SessionStatus::STATUS_COMPLETED;
    }

    public function isExpired()
    {
        return $this->status === SessionStatus::STATUS_EXPIRED;
    }

    public function hasFailed()
    {
        return $this->status === SessionStatus::STATUS_FAILED;
    }

    /**
     * Saves the session's updatable properties.
     *
     * @return \Mollie\Api\Resources\Session
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function update()
    {
        $body = [
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
        ];

        $result = $this->client->sessions->update($this->id, $this->withPresetOptions($body));

        return ResourceFactory::createFromApiResult($result, new Session($this->client));
    }

    /**
     * Cancels this session.
     *
     * @return Session
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function cancel()
    {
        return $this->client->sessions->cancel($this->id, $this->getPresetOptions());
    }

    /**
     * @deprecated The Checkout Sessions API only returns a self link. Use the clientAccessToken with Mollie's client-side components instead.
     *
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
