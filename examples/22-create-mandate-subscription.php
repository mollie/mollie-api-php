<?php
try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "initialize.php";

    /*
     * Customer creation parameters.
     *
     * See: https://docs.mollie.com/reference/v2/customers-api/create-customer
     */
    $customer = $mollie->customers->create(array(
        "name" => 'Example name',
        "email" => 'info@example.com',
    ));

    echo "<p>Customer created with id " . $customer->id . "</p>";

    $mandate = $customer->createMandate(array(
        "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
        "consumerAccount" => 'NL34ABNA0243341423',
        "consumerName" => 'B. A. Example',
    ));

    echo "<p>Mandate created with id " . $mandate->id . "</p>";

    $subscription = $customer->createSubscription(array(
        "amount" => 10.00,
        "times" => 12, // recurring membership for 1 year
        "interval" => "1 months", // every month
        "description" => "Subscription 12345",
        "webhookUrl" => "https://example.com/webhook.php",
        "metadata" => array(
            "order_id" => "12345",
        ),
    ));

    echo "<p>Subscription created with id " . $subscription->id . "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    error_log("API call failed: " . htmlspecialchars($e->getMessage()));
}