<?php

namespace Mollie\Api\Types;

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
class PaymentStatus
{
    /**
     * The payment has just been created, no action has happened on it yet.
     */
    const STATUS_OPEN = "open";

    /**
     * The payment has just been started, no final confirmation yet.
     */
    const STATUS_PENDING = "pending";

    /**
     * The customer has cancelled the payment.
     */
    const STATUS_CANCELLED = "cancelled";

    /**
     * The payment has expired due to inaction of the customer.
     */
    const STATUS_EXPIRED = "expired";

    /**
     * The payment has been paid.
     */
    const STATUS_PAID = "paid";

    /**
     * The payment has been paidout and the money has been transferred to the bank account of the merchant.
     */
    const STATUS_PAIDOUT = "paidout";

    /**
     * The payment has been refunded, either through Mollie or through the payment provider (in the case of PayPal).
     */
    const STATUS_REFUNDED = "refunded";

    /**
     * Some payment methods provide your customers with the ability to dispute payments which could
     * ultimately lead to a chargeback.
     */
    const STATUS_CHARGED_BACK = "charged_back";

    /**
     * The payment has failed.
     */
    const STATUS_FAILED = "failed";
}
