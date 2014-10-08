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
 * @author      Mollie B.V. <info@mollie.nl>
 * @copyright   Mollie B.V.
 * @link        https://www.mollie.nl
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
	 * NULL if the payment is not refunded.
	 *
	 * @var float|NULL
	 */
	public $amountRefunded;

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
	 * Is this payment (partially) refunded?
	 *
	 * @return bool
	 */
	public function isRefunded ()
	{
		return $this->status == self::STATUS_REFUNDED;
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
}
