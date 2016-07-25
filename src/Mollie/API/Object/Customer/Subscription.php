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
 */
class Mollie_API_Object_Customer_Subscription
{
	const STATUS_ACTIVE    = "active";
	const STATUS_PENDING   = "pending";   // Waiting for a valid mandate.
	const STATUS_CANCELLED = "cancelled";
	const STATUS_SUSPENDED = "suspended"; // Active, but mandate became invalid.
	const STATUS_COMPLETED = "completed";

	/**
	 * @var string
	 */
	public $resource;

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $customerId;

	/**
	 * Either "live" or "test" depending on the customer's mode.
	 *
	 * @var string
	 */
	public $mode;

	/**
	 * ISO 8601 format.
	 *
	 * @var string
	 */
	public $createdDatetime;

	/**
	 * @var string
	 */
	public $status;

	/**
	 * @var string
	 */
	public $amount;

	/**
	 * @var int|null
	 */
	public $times;

	/**
	 * @var string
	 */
	public $interval;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string|null
	 */
	public $method;

	/**
	 * ISO 8601 format.
	 *
	 * @var string|null
	 */
	public $cancelledDatetime;

	/**
	 * Contains an optional 'webhookUrl'.
	 *
	 * @var object|null
	 */
	public $links;

	/**
	 * Returns whether the Subscription is valid or not.
	 *
	 * @return bool
	 */
	public function isValid ()
	{
		return $this->status === self::STATUS_ACTIVE;
	}

	/**
	 * Returns whether the Subscription is cancelled or not.
	 *
	 * @return bool
	 */
	public function isCancelled ()
	{
		return $this->status === self::STATUS_CANCELLED;
	}
}
