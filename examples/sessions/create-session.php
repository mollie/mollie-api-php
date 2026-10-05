<?php
/*
 * How to create a new session in the Mollie API.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "../initialize.php";

    /*
     * Generate a unique session id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $sessionId = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF']);

    /*
     * Session creation parameters.
     *
     * See: https://docs.mollie.com/reference/v2/sessions-api/create-session
     */
    $session = $mollie->sessions->create([
        "amount" => [
            "value" => "10.00",
            "currency" => "EUR",
        ],
        "description" => "Order #12345",
        "redirectUrl" => "{$protocol}://{$hostname}{$path}/return.php?order_id={$sessionId}",
        "lines" => [
            [
                "description" => "Product A",
                "quantity" => 1,
                "unitPrice" => [
                    "value" => "10.00",
                    "currency" => "EUR",
                ],
                "totalAmount" => [
                    "value" => "10.00",
                    "currency" => "EUR",
                ],
            ],
        ],
        "requiredCustomerDetails" => [
            "email",
            "billing-address",
        ],
        "payment" => [
            "webhookUrl" => "{$protocol}://{$hostname}{$path}/webhook.php",
        ],
    ]);

    /*
     * Use this token with Mollie's client-side components to render the checkout in your frontend.
     */
    $clientAccessToken = $session->clientAccessToken;

    echo "Client access token: " . htmlspecialchars($clientAccessToken);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
