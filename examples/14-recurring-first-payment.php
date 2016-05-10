<?php
/*
 * Example 14 - How to create a first payment to allow recurring payments later.
 */

try
{
	/*
	 * Initialize the Mollie API library with your API key or OAuth access token.
	 */
	include "initialize.php";

	/*
	 * Retrieve the last created customer for this example.
	 * If no customers are created yet, run example 11.
	 */
	$customer = $mollie->customers->all(0, 1)->data[0];

	/*
	 * Generate a unique order id for this example. It is important to include this unique attribute
	 * in the redirectUrl (below) so a proper return page can be shown to the customer.
	 */
	$order_id = time();

	/*
	 * Determine the url parts to these example files.
	 */
	$protocol = isset($_SERVER['HTTPS']) && strcasecmp('off', $_SERVER['HTTPS']) !== 0 ? "https" : "http";
	$hostname = $_SERVER['HTTP_HOST'];
	$path     = dirname(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF']);

	/*
	 * Customer Payment creation parameters.
	 *
	 * See: https://www.mollie.com/en/docs/reference/customers/create-payment
	 */
	$payment = $mollie->customers_payments->with($customer)->create(array(
		"amount"        => 10.00,
		"description"   => "A first payment for recurring",
		"redirectUrl"   => "{$protocol}://{$hostname}{$path}/03-return-page.php?order_id={$order_id}",
		"webhookUrl"    => "{$protocol}://{$hostname}{$path}/02-webhook-verification.php",

		// Flag this payment as a first payment to allow recurring payments later.
		"recurringType" => Mollie_API_Object_Payment::RECURRINGTYPE_FIRST,
	));

	/*
	 * In this example we store the order with its payment status in a database.
	 */
	database_write($order_id, $payment->status);

	/*
	 * Send the customer off to complete the first payment.
	 *
	 * After completion, the customer will have a pending or valid mandate that can be
	 * used for recurring payments and subscriptions.
	 */
	header("Location: " . $payment->getPaymentUrl());
}
catch (Mollie_API_Exception $e)
{
	echo "API call failed: " . htmlspecialchars($e->getMessage());
}


/*
 * NOTE: This example uses a text file as a database. Please use a real database like MySQL in production code.
 */
function database_write ($order_id, $status)
{
	$order_id = intval($order_id);
	$database = dirname(__FILE__) . "/orders/order-{$order_id}.txt";

	file_put_contents($database, $status);
}
