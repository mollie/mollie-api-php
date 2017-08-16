<?php
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
class Mollie_API_Object_Payment
{
	/**
	 * The payment has just been created, no action has happened on it yet.
	 */
	const STATUS_OPEN      = "open";

	/**
	 * The payment has just been started, no final confirmation yet.
	 */
	const STATUS_PENDING   = "pending";

	/**
	 * The customer has cancelled the payment.
	 */
	const STATUS_CANCELLED = "cancelled";

	/**
	 * The payment has expired due to inaction of the customer.
	 */
	const STATUS_EXPIRED   = "expired";

	/**
	 * The payment has been paid.
	 */
	const STATUS_PAID      = "paid";

	/**
	 * The payment has been paidout and the money has been transferred to the bank account of the merchant.
	 */
	const STATUS_PAIDOUT   = "paidout";

	/**
	 * The payment has been refunded, either through Mollie or through the payment provider (in the case of PayPal).
	 */
	const STATUS_REFUNDED  = "refunded";

	/**
	 * Some payment methods provide your customers with the ability to dispute payments which could
	 * ultimately lead to a chargeback.
	 */
	const STATUS_CHARGED_BACK = "charged_back";

	/**
	 * The payment has failed.
	 */
	const STATUS_FAILED  = "failed";

	/**
	 * Recurring types.
	 *
	 * @see https://www.mollie.com/en/docs/recurring
	 */
	const RECURRINGTYPE_NONE      = NULL;
	const RECURRINGTYPE_FIRST     = "first";
	const RECURRINGTYPE_RECURRING = "recurring";

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
	 * @var float
	 */
	public $amount;

	/**
	 * The amount of the payment that has been refunded to the consumer, in EURO with 2 decimals. This field will be
	 * NULL if the payment can not be refunded.
	 *
	 * @var float|null
	 */
	public $amountRefunded;

	/**
	 * The amount of a refunded payment that can still be refunded, in EURO with 2 decimals. This field will be
	 * NULL if the payment can not be refunded.
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
	 * @see Mollie_API_Object_Method
	 * @var string|null
	 */
	public $method;

	/**
	 * The status of the payment.
	 *
	 * @var string
	 */
	public $status = self::STATUS_OPEN;

	/**
	 * The period after which the payment will expire in ISO-8601 format.
	 *
	 * @example P12DT11H30M45S (12 days, 11 hours, 30 minutes and 45 seconds)
	 * @var string|null
	 */
	public $expiryPeriod;

	/**
	 * Date and time the payment was created in ISO-8601 format.
	 *
	 * @example "2013-12-25T10:30:54.0Z"
	 * @var string|null
	 */
	public $createdDatetime;

	/**
	 * Date and time the payment was paid in ISO-8601 format.
	 *
	 * @var string|null
	 */
	public $paidDatetime;

	/**
	 * Date and time the payment was cancelled in ISO-8601 format.
	 *
	 * @var string|null
	 */
	public $cancelledDatetime;

	/**
	 * Date and time the payment was cancelled in ISO-8601 format.
	 *
	 * @var string|null
	 */
	public $expiredDatetime;

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
	 * Either "first", "recurring", or NULL for regular payments.
	 *
	 * @var string|null
	 */
	public $recurringType;

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
	 * @var object
	 */
	public $links;

	/**
	 * Is this payment cancelled?
	 *
	 * @return bool
	 */
	public function isCancelled ()
	{
		return $this->status == self::STATUS_CANCELLED;
	}

	/**
	 * Is this payment expired?
	 *
	 * @return bool
	 */
	public function isExpired ()
	{
		return $this->status == self::STATUS_EXPIRED;
	}

	/**
	 * Is this payment still open / ongoing?
	 *
	 * @return bool
	 */
	public function isOpen ()
	{
		return $this->status == self::STATUS_OPEN;
	}

	/**
	 * Is this payment pending?
	 *
	 * @return bool
	 */
	public function isPending ()
	{
		return $this->status == self::STATUS_PENDING;
	}

	/**
	 * Is this payment paid for?
	 *
	 * @return bool
	 */
	public function isPaid ()
	{
		return !empty($this->paidDatetime);
	}

	/**
	 * Has the money been transferred to the bank account of the merchant?
	 *
	 * Note: When a payment is refunded or charged back, the status 'refunded'/'charged_back' will
	 * overwrite the 'paidout' status.
	 *
	 * @return bool
	 */
	public function isPaidOut ()
	{
		return $this->status == self::STATUS_PAIDOUT;
	}

	/**
	 * Is this payment (partially) refunded?
	 *
	 * @return bool
	 */
	public function isRefunded ()
	{
		return $this->status == self::STATUS_REFUNDED;
	}

	/**
	 * Is this payment charged back?
	 *
	 * @return bool
	 */
	public function isChargedBack ()
	{
		return $this->status == self::STATUS_CHARGED_BACK;
	}

	/**
	 * Is this payment failing?
	 *
	 * @return bool
	 */
	public function isFailed ()
	{
		return $this->status == self::STATUS_FAILED;
	}

	/**
	 * Check whether the 'recurringType' parameter has been defined for this payment.
	 *
	 * @return bool
	 */
	public function hasRecurringType ()
	{
		return $this->hasRecurringTypeFirst() || $this->hasRecurringTypeRecurring();
	}

	/**
	 * Check whether 'recurringType' is set to 'first'. If a 'first' payment has been completed successfully, the
	 * consumer's account may be charged automatically using recurring payments.
	 *
	 * @return bool
	 */
	public function hasRecurringTypeFirst ()
	{
		return $this->recurringType == self::RECURRINGTYPE_FIRST;
	}

	/**
	 * Check whether 'recurringType' is set to 'recurring'. This type of payment is processed without involving
	 * the consumer.
	 *
	 * @return bool
	 */
	public function hasRecurringTypeRecurring ()
	{
		return $this->recurringType == self::RECURRINGTYPE_RECURRING;
	}

	/**
	 * Get the payment URL where the customer can complete the payment.
	 *
	 * @return string|null
	 */
	public function getPaymentUrl ()
	{
		if (empty($this->links->paymentUrl))
		{
			return NULL;
		}

		return $this->links->paymentUrl;
	}

	/**
	 * @return bool
	 */
	public function canBeRefunded ()
	{
		return $this->amountRemaining !== NULL;
	}

	/**
	 * @return bool
	 */
	public function canBePartiallyRefunded ()
	{
		return $this->canBeRefunded();
	}

	/**
	 * Get the amount that is already refunded
	 *
	 * @return float
	 */
	public function getAmountRefunded ()
	{
		if ($this->amountRefunded)
		{
			return floatval($this->amountRefunded);
		}

		return 0.0;
	}

	/**
	 * Get the remaining amount that can be refunded. For some payment methods this amount can be higher than
	 * the payment amount. This is possible to reimburse the costs for a return shipment to your customer for example.
	 *
	 * @return float
	 */
	public function getAmountRemaining ()
	{
		if ($this->amountRemaining)
		{
			return floatval($this->amountRemaining);
		}

		return 0.0;
	}
}
