<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\Payment;

class PaymentCaptureEndpoint extends EndpointAbstract
{
    protected $resourcePath = "payments_captures";

    /**
     * Get the object that is used by this API endpoint. Every API endpoint uses one type of object.
     *
     * @return Capture
     */
    protected function getResourceObject()
    {
        return new Capture($this->client);
    }

    /**
     * Get the collection object that is used by this API endpoint. Every API endpoint uses one type of collection object.
     *
     * @param int $count
     * @param object[] $_links
     *
     * @return CaptureCollection
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new CaptureCollection($this->client, $count, $_links);
    }

    /**
     * @param Payment $payment
     * @param string $captureId
     * @param array $parameters
     *
     * @return Capture
     */
    public function getFor(Payment $payment, $captureId, array $parameters = [])
    {
        $this->parentId = $payment->id;

        return parent::rest_read($captureId, $parameters);
    }
}
