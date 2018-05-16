<?php
/*
 * Example 17 - How to cancel a subscription.
 */

try {
    /*
     * Initialize the Mollie API library with your API key or OAuth access token.
     */
    require "./initialize.php";
    /**
     * Retrieve the last created customer for this example.
     * If no customers are created yet, run example 11.
     */
    $customer = $mollie->customers->page(null, 1)[0];
    /*
     * Generate a unique subscription id for this example. It is important to include this unique attribute
     * in the webhookUrl (below) so new payments can be associated with this subscription.
     */
    $subscriptionId = isset($_GET['subscription_id']) ? $_GET['subscription_id'] : '';
    /*
     * Customer Subscription deletion parameters.
     *
     * See: https://www.mollie.com/nl/docs/reference/subscriptions/delete
     */
    $canceledSubscription = $customer->cancelSubscription($subscriptionId);
    /*
     * The subscription status should now be canceled
     */
    echo "<p>The subscription status is now: '" . htmlspecialchars($canceledSubscription->status) . "'.</p>\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}