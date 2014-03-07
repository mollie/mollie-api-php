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
 * @method Mollie_API_Object_Payment_Refund[]|Mollie_API_Object_List all($offset = 0, $limit = 0)
 * @method Mollie_API_Object_Payment_Refund get($resource_id)
 */
class Mollie_API_Resource_Payments_Refunds extends Mollie_API_Resource_Base
{
	/**
	 * @var string
	 */
	private $payment_id;

	/**
	 * @return Mollie_API_Object_Method
	 */
	protected function getResourceObject ()
	{
		return new Mollie_API_Object_Payment_Refund;
	}

	/**
	 * @return string
	 */
	protected function getResourceName ()
	{
		return "payments/" . urlencode($this->payment_id) . "/refunds";
	}

	/**
	 * Set the resource to use a certain payment. Use this method before performing a get() or all() call.
	 *
	 * @param Mollie_API_Object_Payment $payment
	 * @return self
	 */
	public function with(Mollie_API_Object_Payment $payment)
	{
		$this->payment_id = $payment->id;
		return $this;
	}
}
