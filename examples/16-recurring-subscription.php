<?php
/*
 * Example 16 - How to create a regular subscription.
 */

try
{
	/*
	 * Initialize the Mollie API library with your API key or OAuth access token.
	 */
	include "initialize.php";

	/**
	 * Retrieve the last created customer for this example.
	 * If no customers are created yet, run example 11.
	 */
	$customer = $mollie->customers->all(0, 1)->data[0];

	/*
	 * Generate a unique subscription id for this example. It is important to include this unique attribute
	 * in the webhookUrl (below) so new payments can be associated with this subscription.
	 */
	$my_subscription = time();

	/*
	 * Customer Subscription creation parameters.
	 *
	 * See: https://www.mollie.com/nl/docs/reference/subscriptions/create
	 */
	$subscription = $mollie->customers_subscriptions->with($customer)->create(array(
		"amount"      => 10.00,
		"times"       => 12,
		"interval"    => "1 month",
		"description" => "My subscription",
		"method"      => NULL,
		"webhookUrl"  => "https://example.org/subscription-payment-webhook/$my_subscription",
	));

	/*
	 * The subscription will be either pending or active depending on whether the customer has
	 * a pending or valid mandate. If the customer has no mandates an error is returned. You
	 * should then set up a "first payment" for the customer (example 14).
	 */

	echo "<p>The subscription status is '" . htmlspecialchars($subscription->status) . "'.</p>\n";
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
