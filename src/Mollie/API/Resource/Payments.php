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
 *
 * @method Mollie_API_Object_Payment[]|Mollie_API_Object_List all($offset = 0, $limit = 0)
 * @method Mollie_API_Object_Payment create(array $data)
 */
class Mollie_API_Resource_Payments extends Mollie_API_Resource_Base
{
	/**
	 * @var string
	 */
	const RESOURCE_ID_PREFIX = 'tr_';

	/**
	 * @return Mollie_API_Object_Payment
	 */
	protected function getResourceObject ()
	{
		return new Mollie_API_Object_Payment;
	}

	/**
	 * Retrieve a single payment from Mollie.
	 *
	 * Will throw a Mollie_API_Exception if the payment id is invalid or the resource cannot be found.
	 *
	 * @param string $payment_id
	 *
	 * @throws Mollie_API_Exception
	 * @return Mollie_API_Object_Payment
	 */
	public function get($payment_id)
	{
		if (empty($payment_id) || strpos($payment_id, self::RESOURCE_ID_PREFIX) !== 0)
		{
			throw new Mollie_API_Exception("Invalid payment ID: '{$payment_id}'. A payment ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
		}

		return parent::get($payment_id);
	}

	/**
	 * @param Mollie_API_Object_Payment $payment
	 * @param float|NULL $amount Amount to refund, or NULL to refund full amount.
	 * @return Mollie_API_Object_Payment_Refund
	 */
	public function refund (Mollie_API_Object_Payment $payment, $amount = NULL)
	{
		$resource = "{$this->getResourceName()}/" . urlencode($payment->id) . "/refunds";

		$body = NULL;
		if ($amount)
		{
			$body = json_encode(
				array("amount" => $amount)
			);
		}

		$result = $this->performApiCall(self::REST_CREATE, $resource, $body);

		/*
		 * Update the payment with the new properties that we got from the refund.
		 */
		if (!empty($result->payment))
		{
			foreach ($result->payment as $payment_key => $payment_value)
			{
				$payment->{$payment_key} = $payment_value;
			}
		}

		return $this->copy($result, new Mollie_API_Object_Payment_Refund);
	}
}
