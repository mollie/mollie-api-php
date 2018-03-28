<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;

/**
 * Copyright (c) 2013, Mollie B.V.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR AND CONTRIBUTORS ``AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @license     Berkeley Software Distribution License (BSD-License 2) http://www.opensource.org/licenses/bsd-license.php
 * @author      Mollie B.V. <info@mollie.com>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.com
 */
class Payment extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the payment (on the Mollie platform).
     *
     * @var string
     */
    public $id;

    /**
     * Mode of the payment, either "live" or "test" depending on the API Key that was used.
     *
     * @var string
     */
    public $mode;

    /**
     * The amount of the payment in EURO with 2 decimals.
     *
     * @var StdClass
     */
    public $amount;

    /**
     * The amount of the payment that has been refunded to the consumer, in EURO with 2 decimals. This field will be
     * null if the payment can not be refunded.
     *
     * @var float|null
     */
    public $amountRefunded;

    /**
     * The amount of a refunded payment that can still be refunded, in EURO with 2 decimals. This field will be
     * null if the payment can not be refunded.
     *
     * For some payment methods this amount can be higher than the payment amount. This is possible to reimburse
     * the costs for a return shipment to your customer for example.
     *
     * @var float|null
     */
    public $amountRemaining;

    /**
     * Description of the payment that is shown to the customer during the payment, and
     * possibly on the bank or credit card statement.
     *
     * @var string
     */
    public $description;

    /**
     * If method is empty/null, the customer can pick his/her preferred payment method.
     *
     * @see Method
     * @var string|null
     */
    public $method;

    /**
     * The status of the payment.
     *
     * @var string
     */
    public $status = PaymentStatus::STATUS_OPEN;

    /**
     * UTC datetime the payment was created in ISO-8601 format.
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
     * UTC datetime the payment was cancelled in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $cancelledAt;

    /**
     * UTC datetime the payment was cancelled in ISO-8601 format.
     *
     * @var string|null
     */
    public $expiresAt;

    /**
     * UTC datetime the payment failed in ISO-8601 format.
     *
     * @var string|null
     */
    public $failedAt;

    /**
     * The profile ID this payment belongs to.
     *
     * @example pfl_xH2kP6Nc6X
     * @var string
     */
    public $profileId;

    /**
     * The customer ID this payment is performed by.
     *
     * @example cst_51EkUqla3
     * @var string|null
     */
    public $customerId;

    /**
     * Either "first", "recurring", or "oneoff" for regular payments.
     *
     * @var string|null
     */
    public $sequenceType;

    /**
     * Redirect URL set on this payment
     *
     * @var string
     */
    public $redirectUrl;

    /**
     * Webhook URL set on this payment
     *
     * @var string
     */
    public $webhookUrl;

    /**
     * The mandate ID this payment is performed with.
     *
     * @example mdt_pXm1g3ND
     * @var string|null
     */
    public $mandateId;

    /**
     * The subscription ID this payment belongs to.
     *
     * @example sub_rVKGtNd6s3
     * @var string|null
     */
    public $subscriptionId;

    /**
     * The locale used for this payment.
     *
     * @var string|null
     */
    public $locale;

    /**
     * During creation of the payment you can set custom metadata that is stored with
     * the payment, and given back whenever you retrieve that payment.
     *
     * @var object|mixed|null
     */
    public $metadata;

    /**
     * Details of a successfully paid payment are set here. For example, the iDEAL
     * payment method will set $details->consumerName and $details->consumerAccount.
     *
     * @var object
     */
    public $details;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * Whether or not this payment can be cancelled.
     *
     * @var bool|null
     */
    public $canBeCancelled;

    /**
     * Is this payment cancelled?
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === PaymentStatus::STATUS_CANCELLED;
    }

    /**
     * Is this payment expired?
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->status === PaymentStatus::STATUS_EXPIRED;
    }

    /**
     * Is this payment still open / ongoing?
     *
     * @return bool
     */
    public function isOpen()
    {
        return $this->status === PaymentStatus::STATUS_OPEN;
    }

    /**
     * Is this payment pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === PaymentStatus::STATUS_PENDING;
    }

    /**
     * Is this payment paid for?
     *
     * @return bool
     */
    public function isPaid()
    {
        return !empty($this->paidAt);
    }

    /**
     * Has the money been transferred to the bank account of the merchant?
     *
     * Note: When a payment is refunded or charged back, the status 'refunded'/'charged_back' will
     * overwrite the 'paidout' status.
     *
     * @return bool
     */
    public function isPaidOut()
    {
        return $this->status === PaymentStatus::STATUS_PAIDOUT;
    }

    /**
     * Is this payment (partially) refunded?
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->status === PaymentStatus::STATUS_REFUNDED;
    }

    /**
     * Is this payment charged back?
     *
     * @return bool
     */
    public function isChargedBack()
    {
        return $this->status === PaymentStatus::STATUS_CHARGED_BACK;
    }

    /**
     * Is this payment failing?
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status === PaymentStatus::STATUS_FAILED;
    }

    /**
     * Check whether 'sequenceType' is set to 'first'. If a 'first' payment has been completed successfully, the
     * consumer's account may be charged automatically using recurring payments.
     *
     * @return bool
     */
    public function hasSequenceTypeFirst()
    {
        return $this->sequenceType === SequenceType::SEQUENCETYPE_FIRST;
    }

    /**
     * Check whether 'sequenceType' is set to 'recurring'. This type of payment is processed without involving
     * the consumer.
     *
     * @return bool
     */
    public function hasSequenceTypeRecurring()
    {
        return $this->sequenceType === SequenceType::SEQUENCETYPE_RECURRING;
    }

    /**
     * Get the checkout URL where the customer can complete the payment.
     *
     * @return string|null
     */
    public function getCheckoutUrl()
    {
        if (empty($this->_links->checkout)) {
            return null;
        }

        return $this->_links->checkout->href;
    }

    /**
     * @return bool
     */
    public function canBeRefunded()
    {
        return $this->amountRemaining !== null;
    }

    /**
     * @return bool
     */
    public function canBePartiallyRefunded()
    {
        return $this->canBeRefunded();
    }

    /**
     * Get the amount that is already refunded
     *
     * @return float
     */
    public function getAmountRefunded()
    {
        if ($this->amountRefunded) {
            return (float)$this->amountRefunded->value;
        }

        return 0.0;
    }

    /**
     * Get the remaining amount that can be refunded. For some payment methods this amount can be higher than
     * the payment amount. This is possible to reimburse the costs for a return shipment to your customer for example.
     *
     * @return float
     */
    public function getAmountRemaining()
    {
        if ($this->amountRemaining) {
            return (float)$this->amountRemaining->value;
        }

        return 0.0;
    }

    /**
     * Retrieves all refunds associated with this payment
     *
     * @return RefundCollection
     */
    public function refunds()
    {
        if(!isset($this->_links->refunds->href)) {
            return new RefundCollection(0, null);
        }

        $result = $this->client->performHttpCallToFullUrl( MollieApiClient::HTTP_GET, $this->_links->refunds->href);

        $resourceCollection = new RefundCollection($result->count, $result->_links);
        foreach ($result->_embedded->refunds as $dataResult) {
            $resourceCollection[] = $this->copy($dataResult, new Refund());
        }

        return $resourceCollection;
    }

    /**
     * Retrieves all chargebacks associated with this payment
     *
     * @return ChargebackCollection
     */
    public function chargebacks()
    {
        if(!isset($this->_links->chargebacks->href)) {
            return new ChargebackCollection(0, null);
        }

        $result = $this->client->performHttpCallToFullUrl( MollieApiClient::HTTP_GET, $this->_links->chargebacks->href);

        $resourceCollection = new ChargebackCollection($result->count, $result->_links);
        foreach ($result->_embedded->chargebacks as $dataResult) {
            $resourceCollection[] = $this->copy($dataResult, new Chargeback());
        }

        return $resourceCollection;
    }
}
