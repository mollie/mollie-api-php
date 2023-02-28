<?php
/*
 * How to prepare a new payment with the Mollie API.
 */

try {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    require_once __DIR__ . "/../vendor/autoload.php";
    require_once __DIR__ . "/functions.php";

    /*
     * Initialize the Mollie API library with your oauth access token.
     *
     * See: https://docs.mollie.com/connect/getting-started
     */

    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken("access_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $orderId = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
    $hostname = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI'] ?? $_SERVER['PHP_SELF']);

    /*
     * Payment parameters:
     *   profileId     Your profileId
     *   amount        Amount in EUROs. This example creates a € 10,- payment.
     *   description   Description of the payment.
     *   redirectUrl   Redirect location. The customer will be redirected there after the payment.
     *   webhookUrl    Webhook location, used to report when the payment changes state.
     *   routing       Routing part of a payment to a connected account https://docs.mollie.com/connect/splitting-payments
     *
     * For example, the funds for the following payment will only become available on the balance of the connected account on 1 January 2025:

     */
    $payment = $mollie->payments->create([
        "profileId" => "pfl_v9hTwCvYqw",
        "amount" => [
            "currency" => "EUR",
            "value" => "10.00", // You must send the correct number of decimals, thus we enforce the use of strings
        ],
        "description" => "Order #{$orderId}",
        "redirectUrl" => "{$protocol}://{$hostname}{$path}/return.php?order_id={$orderId}",
        "webhookUrl" => "{$protocol}://{$hostname}{$path}/webhook.php",
        "routing" => [
            [
                "amount" => [
                    "currency" => "EUR",
                    "value" => "7.50",
                ],
                "destination" => [
                    "type" => "organization",
                    "organizationId" => "org_23456",
                ],
                "releaseDate" => "2025-01-01",
            ],
        ],
    ]);

    /*
     * In this example we store the order with its payment status in a database.
     */
    database_write($orderId, $payment->status);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     */
    header("Location: " . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
