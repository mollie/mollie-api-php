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
 *
 * @method Mollie_API_Object_Payment[]|Mollie_API_Object_List all($offset = 0, $limit = 0, array $filters = array())
 * @method Mollie_API_Object_Payment create(array $data, array $filters = array())
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
	 * @param array $filters
	 * @return Mollie_API_Object_Payment
	 * @throws Mollie_API_Exception
	 */
	public function get ($payment_id, array $filters = array())
	{
		if (empty($payment_id) || strpos($payment_id, self::RESOURCE_ID_PREFIX) !== 0)
		{
			throw new Mollie_API_Exception("Invalid payment ID: '{$payment_id}'. A payment ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
		}

		return parent::get($payment_id, $filters);
	}

	/**
	 * Issue a refund for the given payment.
	 *
	 * The $filters parameter may either be an array of endpoint parameters, a float value to
	 * initiate a partial refund, or empty to do a full refund.
	 *
	 * @param Mollie_API_Object_Payment $payment
	 * @param array|float|NULL $filters
	 * 
	 * @return Mollie_API_Object_Payment_Refund
	 */
	public function refund (Mollie_API_Object_Payment $payment, $filters = array())
	{
		$resource = "{$this->getResourcePath()}/" . urlencode($payment->id) . "/refunds";

		if (!is_array($filters))
		{
			if ((is_numeric($filters))) {
				// $filters is numeric, so it must be an amount
				$filters = array('amount' => $filters);
			}
			else
			{
				// $filters is not an array, but also not an amount, so reset $filters
				$filters = array();
			}
		}

		$body = NULL;
		if (count($filters) > 0)
		{
			$body = json_encode($filters);
		}

		$result = $this->performApiCall(self::REST_CREATE, $resource, $body);

		/*
		 * Update the payment with the new properties that we got from the refund.
		 */
		if (!empty($result->payment))
		{
			$this->copy($result->payment, $payment);
		}

		return $this->copy($result, new Mollie_API_Object_Payment_Refund);
	}
}
