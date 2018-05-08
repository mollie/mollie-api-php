<?php
/*
 * Example 15 - How to create an on-demand recurring payment.
 */
try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";

    /*
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run example 11.
     */
    $customer = $mollie->customers->page(null, 1)[0];

    /*
     * Customer Payment creation parameters.
     *
     * See: https://docs.mollie.com/reference/v2/customers-api/create-customer-payment
     */
    $payment = $customer->createPayment(array(
        "amount" => [
            "value" => "10.00",
            "currency" => "EUR"
        ],
        "description" => "An on-demand recurring payment",

        // Flag this payment as a recurring payment.
        "sequenceType" => \Mollie\Api\Types\SequenceType::SEQUENCETYPE_RECURRING,
    ));

    /*
     * The payment will be either pending or paid immediately. The customer
     * does not have to perform any payment steps.
     */
    echo "<p>Selected mandate is '" . htmlspecialchars($payment->mandateId) . "' (" . htmlspecialchars($payment->method) . ").</p>\n";
    echo "<p>The payment status is '" . htmlspecialchars($payment->status) . "'.</p>\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
