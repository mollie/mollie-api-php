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
class Mollie_API_Object_Payment_Refund
{
	/**
	 * The refund will be send to the bank on the next business day. You can still cancel the refund.
	 */
	const STATUS_PENDING    = 'pending';

	/**
	 * The refund has been sent to the bank. The refund amount will be transferred to the consumer account as soon as possible.
	 */
	const STATUS_PROCESSING = 'processing';

	/**
	 * The refund amount has been transferred to the consumer.
	 */
	const STATUS_REFUNDED   = 'refunded';

	/**
	 * Id of the payment method.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * The $amount that was refunded.
	 *
	 * @var float
	 */
	public $amount;

	/**
	 * The payment that was refunded.
	 *
	 * @var Mollie_API_Object_Payment
	 */
	public $payment;

	/**
	 * Date and time the payment was cancelled in ISO-8601 format.
	 *
	 * @var string|null
	 */
	public $refundedDatetime;

	/**
	 * The refund status
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Is this refund pending?
	 *
	 * @return bool
	 */
	public function isPending ()
	{
		return $this->status == self::STATUS_PENDING;
	}

	/**
	 * Is this refund processing?
	 *
	 * @return bool
	 */
	public function isProcessing ()
	{
		return $this->status == self::STATUS_PROCESSING;
	}

	/**
	 * Is this refund transferred to consumer?
	 *
	 * @return bool
	 */
	public function isTransferred ()
	{
		return $this->status == self::STATUS_REFUNDED;
	}
}
