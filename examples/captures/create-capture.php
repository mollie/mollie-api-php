<?php
/*
 * How to prepare a new payment with the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key.
     *
     * See: https://www.mollie.com/dashboard/developers/api-keys
     */
    require "../initialize.php";

    /*
     * Capture parameters:
     *   amount        Amount in EUROs. This example creates a € 5,- capture. The amount can be the original payment amount or lower.
     *   description   Description of the capture.
     *   metadata      Custom metadata that is stored with the payment.
     */
    $capture = $mollie->paymentCaptures->createForId('tr_WDqYK6vllg', [
        "amount" => [
            "currency" => "EUR",
            "value" => "5.00",
        ],
        "description" => "Order #12345",
    ]);

    echo "<p>New capture created " . htmlspecialchars($capture->id) . " (" . htmlspecialchars($capture->description) . ").</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
