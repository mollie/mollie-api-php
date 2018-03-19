<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Types\RefundStatus;

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
class Refund
{
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
     * UTC datetime the payment was created in ISO-8601 format.
     *
     * @example "2013-12-25T10:30:54+00:00"
     * @var string|null
     */
    public $createdAt;

    /**
     * The refund's description, if available.
     *
     * @var string|null
     */
    public $description;

    /**
     * The payment id that was refunded.
     *
     * @var string
     */
    public $paymentId;

    /**
     * The settlement amount
     *
     * @var object
     */
    public $settlementAmount;

    /**
     * The refund status
     *
     * @var string
     */
    public $status;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * Is this refund queued?
     *
     * @return bool
     */
    public function isQueued()
    {
        return $this->status === RefundStatus::STATUS_QUEUED;
    }

    /**
     * Is this refund pending?
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === RefundStatus::STATUS_PENDING;
    }

    /**
     * Is this refund processing?
     *
     * @return bool
     */
    public function isProcessing()
    {
        return $this->status === RefundStatus::STATUS_PROCESSING;
    }

    /**
     * Is this refund transferred to consumer?
     *
     * @return bool
     */
    public function isTransferred()
    {
        return $this->status === RefundStatus::STATUS_REFUNDED;
    }
}
