<?php
/**
 * Copyright (c) 2016, Mollie B.V.
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
 * @method Mollie_API_Object_Customer_Subscription[]|Mollie_API_Object_List all($offset = 0, $limit = 0, array $filters = array())
 * @method Mollie_API_Object_Customer_Subscription get($subscription_id, array $filters = array())
 */
class Mollie_API_Resource_Customers_Subscriptions extends Mollie_API_Resource_Base
{
	/**
	 * @var string
	 */
	protected $resource_path = "customers_subscriptions";

	/**
	 * @return Mollie_API_Object_Customer_Subscription
	 */
	protected function getResourceObject ()
	{
		return new Mollie_API_Object_Customer_Subscription;
	}

		/**
	 * @param Mollie_API_Object_Customer_Subscription $subscription
	 *
	 * @return Mollie_API_Object_Customer_Subscription|string
	 * @throws Mollie_API_Exception
	 */
	public function cancel ($subscription)
	{
		if($subscription instanceof Mollie_API_Object_Customer_Subscription){
			$result = $this->delete($subscription->id);
		} else {
			$result = $this->delete($subscription);
		}

		return $this->copy($result, new Mollie_API_Object_Customer_Subscription);
	}
}
