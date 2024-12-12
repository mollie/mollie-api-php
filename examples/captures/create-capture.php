<?php

/*
 * How to prepare a new payment with the Mollie API.
 */

use Mollie\Api\Http\Data\CreatePaymentCapturePayload;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require '../initialize.php';

    /*
     * Capture parameters:
     *   amount        Amount in EUROs. This example creates a € 5,- capture. The amount can be the original payment amount or lower.
     *   description   Description of the capture.
     *   metadata      Custom metadata that is stored with the payment.
     */
    $response = $mollie->send(new CreatePaymentCaptureRequest('tr_WDqYK6vllg', new CreatePaymentCapturePayload(
        'Order #12345',
        new Money('EUR', '5.00')
    )));

    $capture = $response->toResource();

    echo '<p>New capture created ' . htmlspecialchars($capture->id) . ' (' . htmlspecialchars($capture->description) . ').</p>';
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: ' . htmlspecialchars($e->getMessage());
}
