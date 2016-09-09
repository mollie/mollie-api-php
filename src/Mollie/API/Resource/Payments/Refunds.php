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
 * @method Mollie_API_Object_Payment_Refund[]|Mollie_API_Object_List all($offset = 0, $limit = 0, array $filters = array())
 * @method Mollie_API_Object_Payment_Refund get($resource_id, array $filters = array())
 */
class Mollie_API_Resource_Payments_Refunds extends Mollie_API_Resource_Base
{
	/**
	 * @var string
	 */
	protected $resource_path = "payments_refunds";

	/**
	 * @return Mollie_API_Object_Method
	 */
	protected function getResourceObject ()
	{
		return new Mollie_API_Object_Payment_Refund;
	}
    
    /**
     * Cancel a refund.
     *
     * @param \Mollie_API_Object_Payment_Refund $refund
     *
     * @return bool
     * @throws \Mollie_API_Exception
     */
    public function cancel(Mollie_API_Object_Payment_Refund $refund)
    {
        $refundId = urlencode($refund->id);
        $paymentId = urlencode($refund->payment->id);
        
        $body = $this->api->performHttpCall(self::REST_DELETE, "payments/{$paymentId}/refunds/{$refundId}", null);
        
        if (!empty( $body ) || $body === false)
        {
            throw new Mollie_API_Exception("Unable to cancel refund.");
        }
        
        return true;
    }
}
