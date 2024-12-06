<?php

/*
 * How to create a first payment to allow recurring payments later.
 */

use Mollie\Api\Factories\CreatePaymentPayloadFactory;
use Mollie\Api\Http\Payload\Metadata;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Http\Requests\GetPaginatedCustomerRequest;
use Mollie\Api\Types\SequenceType;

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require '../initialize.php';

    /*
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run the create-customer example.
     */
    $customer = $mollie->send(new GetPaginatedCustomerRequest)->toResource()[0];

    /*
     * Generate a unique order id for this example. It is important to include this unique attribute
     * in the redirectUrl (below) so a proper return page can be shown to the customer.
     */
    $orderId = time();

    /*
     * Determine the url parts to these example files.
     */
    $protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? 'https' : 'http';
    $hostname = $_SERVER['HTTP_HOST'];

    /**
     * Customer Payment creation parameters.
     *
     * @See: https://docs.mollie.com/reference/v2/customers-api/create-customer-payment
     */
    $payload = CreatePaymentPayloadFactory::new([
        'description' => "First payment - Order #{$orderId}",
        'amount' => new Money('EUR', '10.00'),
        'redirectUrl' => "{$protocol}://{$hostname}/payments/return.php?order_id={$orderId}",
        'webhookUrl' => "{$protocol}://{$hostname}/payments/webhook.php",
        'metadata' => new Metadata([
            'order_id' => $orderId,
        ]),
        'sequenceType' => SequenceType::FIRST,
    ])->create();

    $payment = $mollie->send(
        new CreateCustomerPaymentRequest($customer->id, $payload)
    );

    /*
     * In this example we store the order with its payment status in a database.
     */
    database_write($orderId, $payment->status);

    /*
     * Send the customer off to complete the payment.
     * This request should always be a GET, thus we enforce 303 http response code
     *
     * After completion, the customer will have a pending or valid mandate that can be
     * used for recurring payments and subscriptions.
     */
    header('Location: '.$payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: '.htmlspecialchars($e->getMessage());
}
