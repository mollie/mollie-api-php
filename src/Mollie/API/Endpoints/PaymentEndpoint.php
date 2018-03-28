<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseCollection;
use Mollie\Api\Resources\Chargeback;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\RefundCollection;

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
 * @method Payment[]|PaymentCollection all($from = null, $limit = 50, array $filters = [])
 * @method Payment create(array $data, array $filters = [])
 * @method Payment delete($paymentId)
 */
class PaymentEndpoint extends EndpointAbstract
{
    protected $resourcePath = "payments";

    /**
     * @var string
     */
    const RESOURCE_ID_PREFIX = 'tr_';

    /**
     * @return Payment
     */
    protected function getResourceObject()
    {
        return new Payment($this->api);
    }

    /**
     * Retrieve a single payment from Mollie.
     *
     * Will throw a ApiException if the payment id is invalid or the resource cannot be found.
     *
     * @param string $paymentId
     * @param array $filters
     * @return Payment
     * @throws ApiException
     */
    public function get($paymentId, array $filters = [])
    {
        if (empty($paymentId) || strpos($paymentId, self::RESOURCE_ID_PREFIX) !== 0) {
            throw new ApiException("Invalid payment ID: '{$paymentId}'. A payment ID should start with '" . self::RESOURCE_ID_PREFIX . "'.");
        }

        return parent::get($paymentId, $filters);
    }

    /**
     * Issue a refund for the given payment.
     *
     * The $filters parameter may either be an array of endpoint parameters, a float value to
     * initiate a partial refund, or empty to do a full refund.
     *
     * @param Payment $payment
     * @param array|float|null $data
     *
     * @return object
     */
    public function refund(Payment $payment, $data = [])
    {
        $resource = "{$this->getResourcePath()}/" . urlencode($payment->id) . "/refunds";

        $body = null;
        if (count($data) > 0) {
            $body = json_encode($data);
        }

        $result = $this->api->performHttpCall(self::REST_CREATE, $resource, $body);
        return $this->copy($result, new Refund());
    }

    /**
     * Cancel the given Payment. This is just an alias of the 'delete' method.
     *
     * @param string $paymentId
     *
     * @return Payment
     * @throws ApiException
     */
    public function cancel($paymentId)
    {
        return $this->delete($paymentId);
    }

    /**
     * Get the collection object that is used by this API. Every API uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return BaseCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PaymentCollection($count, $_links);
    }
}
