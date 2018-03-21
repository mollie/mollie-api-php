<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\BaseCollection;
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
 * @method Refund[]|RefundCollection all($offset = 0, $limit = 0, array $filters = [])
 * @method Refund get($refundId, array $filters = [])
 * @method Refund create(array $data = [], array $filters = [])
 * @method Refund delete($refundId)
 */
class PaymentRefundEndpoint extends EndpointAbstract
{
    /**
     * @var string
     */
    protected $resourcePath = "payments_refunds";

    /**
     * @return Refund
     */
    protected function getResourceObject()
    {
        return new Refund();
    }

    /**
     * Cancel the given Refund. This is just an alias of the 'delete' method.
     *
     * @param string $refundId
     *
     * @return Refund
     * @throws ApiException
     */
    public function cancel($refundId)
    {
        return $this->delete($refundId);
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
        return new RefundCollection($count, $_links);
    }
}
