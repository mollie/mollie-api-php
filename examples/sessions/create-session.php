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
        "paymentData" => [
            "amount" => [
                "value" => "10.00",
                "currency" => "EUR",
            ],
            "description" => "Order #12345",
        ],
        "method" => "paypal",
        "methodDetails" => [
            "checkoutFlow" => "express",
        ],
        "returnUrl" => "{$protocol}://{$hostname}{$path}/shippingSelection.php?order_id={$sessionId}",
        "cancelUrl" => "{$protocol}://{$hostname}{$path}/cancel.php?order_id={$sessionId}",
    ]);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $session->getRedirectUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
