<?php
/*
 * Example 22 - Create a customer, mandate and subscription via the Mollie API.
 */

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
    $customer = $mollie->customers->create([
        "name" => 'Example name',
        "email" => 'info@example.com',
    ]);

    echo "<p>Customer created with id " . $customer->id . "</p>";

    $mandate = $customer->createMandate([
        "method" => \Mollie\Api\Types\MandateMethod::DIRECTDEBIT,
        "consumerAccount" => 'NL34ABNA0243341423',
        "consumerName" => 'B. A. Example',
    ]);

    echo "<p>Mandate created with id " . $mandate->id . "</p>";

    /*
     * Generate a unique subscription id for this example. It is important to include this unique attribute
     * in the webhookUrl (below) so new payments can be associated with this subscription.
     */
    $subscription_id = time();

    $subscription = $customer->createSubscription([
        "amount" => [
            "value" => "10.00", // You must send the correct number of decimals, thus we enforce the use of strings
            "currency" => "EUR"
        ],
        "times" => 12, // recurring membership for 1 year
        "interval" => "1 months", // every month
        "description" => "Subscription #{$subscription_id}",
        "webhookUrl" => "https://example.com/webhook.php?subscription_id={$subscription_id}",
        "metadata" => [
            "subscription_id" => $subscription_id,
        ],
    ]);

    echo "<p>Subscription created with id " . $subscription->id . "</p>";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}