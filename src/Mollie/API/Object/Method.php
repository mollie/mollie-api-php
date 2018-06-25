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
class Mollie_API_Object_Method
{
	/**
	 * @link https://www.mollie.com/en/payments/bank-transfer
	 */
	const BANKTRANSFER = "banktransfer";

	/**
	 * @link https://www.mollie.com/en/payments/belfius
	 */
	const BELFIUS = "belfius";

	/**
	 * @link https://www.mollie.com/en/payments/bitcoin
	 */
	const BITCOIN = "bitcoin";

	/**
	 * @link https://www.mollie.com/en/payments/credit-card
	 */
	const CREDITCARD = "creditcard";

	/**
	 * @link https://www.mollie.com/en/payments/direct-debit
	 */
	const DIRECTDEBIT = "directdebit";

	/**
	 * @link https://www.mollie.com/en/payments/eps
	 */
	const EPS = "eps";

	/**
	 * @link https://www.mollie.com/en/payments/gift-cards
	 */
	const GIFTCARD = "giftcard";

	/**
	 * @link https://www.mollie.com/en/payments/giropay
	 */
	const GIROPAY = "giropay";

	/**
	 * @link https://www.mollie.com/en/payments/ideal
	 */
	const IDEAL = "ideal";

	/**
	 * @link https://www.mollie.com/en/payments/ing-homepay
	 */
	const INGHOMEPAY = "inghomepay";

	/**
	 * @link https://www.mollie.com/en/payments/kbc-cbc
	 */
	const KBC = "kbc";

	/**
	 * Bancontact, formerly known as Mister Cash.
	 *
	 * @link https://mollie.com/mistercash
	 */
	const MISTERCASH = "mistercash";

	/**
	 * @link https://www.mollie.com/en/payments/paypal
	 */
	const PAYPAL = "paypal";

	/**
	 * @link https://www.mollie.com/en/payments/paysafecard
	 */
	const PAYSAFECARD = "paysafecard";

	/**
	 * @deprecated
	 * @link https://www.mollie.com/en/payments/gift-cards
	 */
	const PODIUMCADEAUKAART = "podiumcadeaukaart";

	/**
	 * @link https://www.mollie.com/en/payments/sofort
	 */
	const SOFORT = "sofort";

	/**
	 * This is a special method that indicates in the API that the payment consists of several partial transactions.
	 *
	 * The individual transactions (amount, method and details) can be found in the details property of the payment.
	 *
	 * Note that you cannot use this method to create payments.
	 *
	 * @internal
	 */
	const STACKED = "stacked";

	/**
	 * Id of the payment method.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * More legible description of the payment method.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The $amount->minimum and $amount->maximum supported by this method and the used API key.
	 *
	 * @var object
	 */
	public $amount;

	/**
	 * The $image->normal and $image->bigger to display the payment method logo.
	 *
	 * @var object
	 */
	public $image;

	/**
	 * @return float|null
	 */
	public function getMinimumAmount ()
	{
		if (empty($this->amount))
		{
			return NULL;
		}

		return (float) $this->amount->minimum;
	}

	/**
	 * @return float|null
	 */
	public function getMaximumAmount ()
	{
		if (empty($this->amount))
		{
			return NULL;
		}

		return (float) $this->amount->maximum;
	}
}
