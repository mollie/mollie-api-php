<?php
/*
 * Example 17 - How to cancel a subscription.
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
	$subscriptionId = isset($_GET['subscription_id']) ? $_GET['subscription_id'] : '';

	/*
	 * Customer Subscription deletion parameters.
	 *
	 * See: https://www.mollie.com/nl/docs/reference/subscriptions/delete
	 */
	$cancelledSubscription = $mollie->customers_subscriptions->with($customer)->cancel($subscriptionId);

	/*
	 * The subscription status should now be cancelled
	 */
	echo "<p>The subscription status is now: '" . htmlspecialchars($cancelledSubscription->status) . "'.</p>\n";
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}
